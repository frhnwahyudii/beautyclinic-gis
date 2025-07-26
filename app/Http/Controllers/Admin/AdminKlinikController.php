<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminKlinikController extends Controller
{
    public function index()
    {
        $kliniks = Klinik::latest()->paginate(10);
        return view('admin.kliniks.index', compact('kliniks'));
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
        ]);

        if ($request->hasFile('foto')) {
            if ($klinik->foto) {
                Storage::disk('public')->delete('klinik_photos/' . $klinik->foto);
            }
            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama) . '.' . $foto->getClientOriginalExtension();
            $foto->storeAs('klinik_photos', $fotoName, 'public');
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
