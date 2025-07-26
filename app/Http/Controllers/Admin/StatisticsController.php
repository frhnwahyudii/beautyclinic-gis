<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klinik;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        // Get trend data (last 30 days)
        $trendData = Klinik::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get status distribution
        $statusData = [
            'pending' => Klinik::where('status', 'pending')->count(),
            'approved' => Klinik::where('status', 'approved')->count(),
            'rejected' => Klinik::where('status', 'rejected')->count(),
        ];

        // Get location stats (top 10 cities)
        $locationStats = Klinik::select(DB::raw('COUNT(*) as count'), DB::raw('SUBSTRING_INDEX(alamat, ",", -2) as city'))
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Get recent activities (last 10)
        $recentActivities = Klinik::latest()
            ->take(10)
            ->get()
            ->map(function ($klinik) {
                return (object)[
                    'created_at' => $klinik->created_at,
                    'description' => "Klinik {$klinik->nama} telah mendaftar"
                ];
            });

        return view('admin.statistics', compact('trendData', 'statusData', 'locationStats', 'recentActivities'));
    }
}
