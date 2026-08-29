<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminKlinikController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Klinik::query();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('alamat', 'like', "%{$search}%")
                      ->orWhere('telepon', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $kliniks = $query->latest()->paginate(10);

            // Preserve query parameters in pagination
            $kliniks->appends($request->query());

            // Debug: Add some logging or session flash for debugging
            if (config('app.debug')) {
                session()->flash('debug', 'Loaded ' . $kliniks->total() . ' clinics');
            }

            return view('admin.kliniks.index', compact('kliniks'));
        } catch (\Exception $e) {
            // Log error and redirect with error message
            \Log::error('Admin Klinik Index Error: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat memuat data klinik: ' . $e->getMessage());
        }
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
            'deskripsi' => 'nullable|string|max:1000',
            'min_price' => 'required|integer|min:0',
            'max_price' => 'nullable|integer|min:0|gte:min_price',
            'services' => 'required|array|min:1',
            'services.*' => 'in:facial_basic,facial_acne,facial_brightening,blackhead_removal,hydrafacial,chemical_peel,carbon_peel,milk_peel,laser_rejuvenation,laser_acne,ipl_photorejuvenation,laser_hair_removal,co2_laser,botox,filler,skinbooster,vitamin_injection,whitening_injection,microneedling,rf_microneedling,hifu,prp_therapy,thread_lift,cryotherapy,sclerotherapy,body_contouring,cavitation,radiofrequency,coolsculpting',
            'prices' => 'required|array',
            'prices.*' => 'nullable|integer|min:0'
        ]);

        if ($request->hasFile('foto')) {
            if ($klinik->foto) {
                Storage::disk(config('filesystems.public_disk'))->delete('klinik_photos/' . $klinik->foto);
            }
            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('klinik_photos', $fotoName, config('filesystems.public_disk'));
            $validatedData['foto'] = $fotoName;
        }

        // Menyimpan prices ke service_prices
        $validatedData['service_prices'] = $request->prices;

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
