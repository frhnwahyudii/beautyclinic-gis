<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klinik;
use Illuminate\Http\Request;

class KlinikController extends Controller
{
    public function index()
    {
        $kliniks = Klinik::where('status', 'approved')
            ->select('id', 'nama', 'alamat', 'latitude', 'longitude', 'foto', 'deskripsi', 'min_price', 'max_price', 'services', 'jam_operasional', 'telepon')
            ->get()
            ->map(function($klinik) {
                return [
                    'id' => $klinik->id,
                    'nama' => $klinik->nama,
                    'alamat' => $klinik->alamat,
                    'latitude' => $klinik->latitude,
                    'longitude' => $klinik->longitude,
                    'foto_url' => $klinik->foto_url,
                    'deskripsi' => $klinik->deskripsi,
                    'price_range' => $klinik->price_range_display,
                    'services' => $klinik->formatted_services,
                    'jam_operasional' => $klinik->jam_operasional,
                    'telepon' => $klinik->telepon
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $kliniks
        ]);
    }
}
