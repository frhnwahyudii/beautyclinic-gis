<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klinik;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        try {
            // Hitung jumlah klinik terdaftar
            $totalKlinik = Klinik::where('status', 'approved')->count();

            // Hitung jumlah jenis layanan unik
            $allServices = Klinik::whereNotNull('services')->pluck('services')->filter()->toArray();
            $uniqueServices = collect();

            foreach ($allServices as $services) {
                if (is_array($services)) {
                    $uniqueServices = $uniqueServices->merge($services);
                }
            }

            $totalLayanan = $uniqueServices->unique()->count();

            // Hitung kategori harga berdasarkan range
            $budgetCount = Klinik::where('max_price', '<=', 500000)->count();
            $menengahCount = Klinik::where('max_price', '>', 500000)
                                  ->where('max_price', '<=', 2000000)->count();
            $premiumCount = Klinik::where('max_price', '>', 2000000)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_klinik' => $totalKlinik,
                    'total_layanan' => $totalLayanan,
                    'kategori_harga' => [
                        'budget' => $budgetCount,
                        'menengah' => $menengahCount,
                        'premium' => $premiumCount
                    ],
                    'total_kota' => 1 // Semua di Jambi
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function klinikDetail()
    {
        try {
            // Statistik detail klinik
            $kliniks = Klinik::where('status', 'approved')
                           ->select('nama', 'alamat', 'min_price', 'max_price', 'services')
                           ->get();

            // Group by kategori harga berdasarkan range
            $byKategori = [
                'budget' => $kliniks->where('max_price', '<=', 500000)->count(),
                'menengah' => $kliniks->where('max_price', '>', 500000)
                                   ->where('max_price', '<=', 2000000)->count(),
                'premium' => $kliniks->where('max_price', '>', 2000000)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'total_klinik' => $kliniks->count(),
                    'by_kategori' => $byKategori,
                    'klinik_list' => $kliniks->map(function ($klinik) {
                        $kategori = 'budget';
                        if ($klinik->max_price > 500000 && $klinik->max_price <= 2000000) {
                            $kategori = 'menengah';
                        } elseif ($klinik->max_price > 2000000) {
                            $kategori = 'premium';
                        }

                        return [
                            'nama' => $klinik->nama,
                            'alamat' => $klinik->alamat,
                            'kategori_harga' => $kategori,
                            'price_range' => $klinik->price_range_display,
                            'services_count' => count($klinik->services ?? [])
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail klinik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
