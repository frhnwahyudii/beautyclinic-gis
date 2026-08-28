<?php

namespace App\Http\Controllers;

use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KlinikController extends Controller
{
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
        // ── Anti-Bot: Honeypot field — bot mengisi field tersembunyi, manusia tidak ──
        if ($request->filled('company_website')) {
            // Balas sukses palsu agar bot tidak tahu form diblokir
            return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
        }

        // ── Anti-Bot: Time-trap — submit terlalu cepat dianggap bot ──
        $formStartedAt = (int) $request->input('form_started_at', 0);
        if ($formStartedAt > 0 && (time() - $formStartedAt) < 5) {
            return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
        }

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:max_width=4000,max_height=4000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jam_operasional' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'min_price' => 'required|integer|min:0',
            'max_price' => 'nullable|integer|min:0|gte:min_price',
            'services' => 'required|array|min:1',
            'services.*' => 'in:facial_basic,facial_acne,facial_brightening,blackhead_removal,hydrafacial,chemical_peel,carbon_peel,milk_peel,laser_rejuvenation,laser_acne,ipl_photorejuvenation,laser_hair_removal,co2_laser,botox,filler,skinbooster,vitamin_injection,whitening_injection,microneedling,rf_microneedling,hifu,prp_therapy,thread_lift,cryotherapy,sclerotherapy,body_contouring,cavitation,radiofrequency,coolsculpting'
        ]);

        // Normalisasi input teks
        $validatedData['nama'] = trim($validatedData['nama']);
        $validatedData['alamat'] = trim($validatedData['alamat']);
        if (!empty($validatedData['email'])) {
            $validatedData['email'] = strtolower(trim($validatedData['email']));
        }

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/klinik_photos', $fotoName);
            $validatedData['foto'] = $fotoName;
        }

        Klinik::create($validatedData);

        return redirect()->route('home')->with('success', 'Data klinik berhasil dikirim dan menunggu persetujuan admin.');
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
                Storage::delete('public/klinik_photos/' . $klinik->foto);
            }

            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('public/klinik_photos', $fotoName);
            $validatedData['foto'] = $fotoName;
        }

        $klinik->update($validatedData);

        return redirect()->route('admin.kliniks.index')
            ->with('success', 'Data klinik berhasil diperbarui.');
    }

    public function destroy(Klinik $klinik)
    {
        if ($klinik->foto) {
            Storage::delete('public/klinik_photos/' . $klinik->foto);
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
