<?php

namespace App\Http\Controllers;

use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KlinikController extends Controller
{
    /** Waktu minimum mengisi form (detik) untuk menyaring bot. */
    private const MIN_FILL_SECONDS = 8;

    public function index()
    {
        $kliniks = Klinik::where('status', 'approved')->get();
        return view('public.map', compact('kliniks'));
    }

    public function adminIndex()
    {
        $kliniks = Klinik::latest()->paginate(10);
        return view('admin.kliniks.index', compact('kliniks'));
    }

    public function create()
    {
        return view('public.form');
    }

    public function store(Request $request)
    {
        // ── Anti-Bot: Honeypot ganda — bot mengisi field tersembunyi, manusia tidak ──
        if ($request->filled('company_website') || $request->filled('fax_number')) {
            // Balas sukses palsu agar bot tidak tahu form diblokir
            return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
        }

        // ── Anti-Bot: Time-trap — submit terlalu cepat atau tanpa tanda waktu = bot ──
        $formStartedAt = (int) $request->input('form_started_at', 0);
        if ($formStartedAt <= 0 || (time() - $formStartedAt) < self::MIN_FILL_SECONDS) {
            return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
        }

        // ── Anti-Bot: Cloudflare Turnstile (opsional — aktif bila TURNSTILE_SECRET_KEY diset) ──
        if (config('services.turnstile.secret')) {
            $verify = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => (string) $request->input('cf-turnstile-response', ''),
                'remoteip' => $request->ip(),
            ])->json();

            if (! ($verify['success'] ?? false)) {
                return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
            }
        }

        // ── Normalisasi sebelum validasi agar format tersimpan konsisten ──
        $nama = $this->normalizeText($request->input('nama'));
        if ($nama === null || $this->isGibberish($nama)) {
            return back()->withErrors(['nama' => 'Nama klinik tidak valid.'])->withInput();
        }

        $request->merge([
            'nama' => $nama,
            'alamat' => $this->normalizeText($request->input('alamat')) ?? '',
            'jam_operasional' => $this->normalizeText($request->input('jam_operasional')) ?? '',
            'deskripsi' => $this->normalizeText($request->input('deskripsi')),
            'telepon' => $this->normalizePhone((string) $request->input('telepon')),
            'email' => strtolower(trim((string) $request->input('email'))),
            'instagram' => $this->normalizeHandle((string) $request->input('instagram')),
            'facebook' => $this->normalizeHandle((string) $request->input('facebook')),
            'twitter' => $this->normalizeHandle((string) $request->input('twitter')),
        ]);

        // ── Validasi ketat: format data wajib masuk akal (anti data fiktif) ──
        $validatedData = $request->validate([
            'nama' => ['required', 'string', 'min:3', 'max:255', 'regex:/[a-zA-Z]/'],
            'alamat' => ['required', 'string', 'min:10', 'max:500', 'regex:/[a-zA-Z0-9]/', 'not_regex:/<[^>]*>/'],
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=200,min_height=200,max_width=4000,max_height=4000',
            'latitude' => 'required|numeric|between:-1.70,-1.50',
            'longitude' => 'required|numeric|between:103.50,103.75',
            'jam_operasional' => ['required', 'string', 'min:5', 'max:255', 'not_regex:/<[^>]*>/'],
            'telepon' => ['nullable', 'string', 'max:20', 'regex:/^(\+?62|0)[0-9]{8,13}$/'],
            'email' => 'nullable|email|max:255',
            'instagram' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_.]{3,255}$/', 'not_regex:/<[^>]*>/'],
            'facebook' => ['nullable', 'string', 'max:255', 'not_regex:/<[^>]*>/'],
            'twitter' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_.]{3,255}$/', 'not_regex:/<[^>]*>/'],
            'website' => 'nullable|url|max:255',
            'deskripsi' => ['nullable', 'string', 'min:20', 'max:1000', 'not_regex:/<[^>]*>/'],
            'min_price' => 'required|integer|min:0|max:100000000',
            'max_price' => 'nullable|integer|min:0|gte:min_price|max:200000000',
            'services' => 'required|array|min:1',
            'services.*' => 'in:facial_basic,facial_acne,facial_brightening,blackhead_removal,hydrafacial,chemical_peel,carbon_peel,milk_peel,laser_rejuvenation,laser_acne,ipl_photorejuvenation,laser_hair_removal,co2_laser,botox,filler,skinbooster,vitamin_injection,whitening_injection,microneedling,rf_microneedling,hifu,prp_therapy,thread_lift,cryotherapy,sclerotherapy,body_contouring,cavitation,radiofrequency,coolsculpting',
            'prices' => 'required|array',
            'prices.*' => 'nullable|integer|min:0|max:100000000',
        ]);

        // ── Anti-Duplikat: kontak yang sama menandakan spam / data fiktif ──
        $phone = $validatedData['telepon'] ?? null;
        $email = !empty($validatedData['email']) ? strtolower($validatedData['email']) : null;
        $namaLower = strtolower($validatedData['nama']);

        $duplicate = Klinik::where(function ($q) use ($phone, $email, $namaLower) {
            if ($phone) {
                $q->orWhere('telepon', $phone);
            }
            if ($email) {
                $q->orWhere(function ($q2) use ($email, $namaLower) {
                    $q2->whereRaw('LOWER(email) = ?', [$email]);
                    $q2->whereRaw('LOWER(nama) = ?', [$namaLower]);
                });
            }
        })->exists();

        if ($duplicate) {
            return back()
                ->withErrors(['telepon' => 'Data dengan nomor telepon/email yang sama sudah pernah didaftarkan. Jika itu klinik Anda, silakan hubungi admin untuk konfirmasi.'])
                ->withInput();
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($validatedData['nama']) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('klinik_photos', $fotoName, config('filesystems.public_disk'));
            $validatedData['foto'] = $fotoName;
        }

        $validatedData['service_prices'] = $request->input('prices', []);

        Klinik::create($validatedData);

        return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
    }

    /** Deteksi teks tidak bermakna (mis. "aaaaa" / tanpa vokal) untuk nama. */
    private function isGibberish(string $value): bool
    {
        $compact = preg_replace('/\s+/', '', $value);
        if (mb_strlen($compact) < 3) {
            return true;
        }
        if (mb_strlen($compact) > 5 && ! preg_match('/[aiueoAIUEO]/', $compact)) {
            return true;
        }

        return count(array_unique(mb_str_split(mb_strtolower($compact)))) === 1;
    }

    /** Rapikan teks: trim & ganti spasi beruntun dengan satu spasi. */
    private function normalizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim(preg_replace('/\s+/', ' ', $value));

        return $value === '' ? null : $value;
    }

    /** Normalkan nomor telepon menjadi digit (opsional awalan +62 / 62 / 0). */
    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $hasPlus = str_starts_with($phone, '+');
        $digits = preg_replace('/[^0-9]/', '', $phone);

        return ($hasPlus ? '+' : '') . $digits;
    }

    /** Buang tanda "@" di depan handle media sosial. */
    private function normalizeHandle(string $value): string
    {
        return ltrim(trim($value), '@');
    }

    public function show(Klinik $klinik)
    {
        return view('public.detail', compact('klinik'));
    }

    public function edit(Klinik $klinik)
    {
        return view('admin.kliniks.edit', compact('klinik'));
    }

    public function update(Request $request, Klinik $klinik)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jam_operasional' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'required|in:pending,approved,rejected',
            'services' => 'required|array|min:1',
            'services.*' => 'in:facial_basic,facial_acne,facial_brightening,blackhead_removal,hydrafacial,chemical_peel,carbon_peel,milk_peel,laser_rejuvenation,laser_acne,ipl_photorejuvenation,laser_hair_removal,co2_laser,botox,filler,skinbooster,vitamin_injection,whitening_injection,microneedling,rf_microneedling,hifu,prp_therapy,thread_lift,cryotherapy,sclerotherapy,body_contouring,cavitation,radiofrequency,coolsculpting',
            'prices' => 'required|array',
            'prices.*' => 'nullable|integer|min:0'
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($klinik->foto) {
                Storage::disk(config('filesystems.public_disk'))->delete('klinik_photos/' . $klinik->foto);
            }

            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('klinik_photos', $fotoName, config('filesystems.public_disk'));
            $validatedData['foto'] = $fotoName;
        }

        $klinik->update($validatedData);

        return redirect()->route('admin.kliniks.index')
            ->with('success', 'Data klinik berhasil diperbarui.');
    }

    public function destroy(Klinik $klinik)
    {
        if ($klinik->foto) {
            Storage::disk(config('filesystems.public_disk'))->delete('klinik_photos/' . $klinik->foto);
        }

        $klinik->delete();

        return redirect()->route('admin.kliniks.index')
            ->with('success', 'Data klinik berhasil dihapus.');
    }

    public function updateStatus(Request $request, Klinik $klinik)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $klinik->update($validatedData);

        return redirect()->route('admin.kliniks.index')
            ->with('success', 'Status klinik berhasil diperbarui.');
    }
}
