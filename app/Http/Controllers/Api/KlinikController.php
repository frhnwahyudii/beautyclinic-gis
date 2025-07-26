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
            ->select('id', 'nama', 'alamat', 'latitude', 'longitude', 'foto')
            ->get();

        return response()->json($kliniks);
    }
}
