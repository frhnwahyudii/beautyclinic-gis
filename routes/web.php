<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\Klinik;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:register');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Public Routes
Route::get('/', function () {
    return view('landing');
})->name('home');

// Sitemap dinamis (hanya URL yang boleh diindeks).
// Ditempatkan sebelum rute /klinik/{klinik} agar tidak tertangkap parameter.
Route::get('/sitemap.xml', function () {
    $latestChange = Klinik::where('status', 'approved')->max('updated_at');
    $latestLastmod = $latestChange ? Carbon::parse($latestChange)->toDateString() : null;

    $urls = [
        [
            'loc' => route('home'),
            'lastmod' => $latestLastmod,
            'changefreq' => 'daily',
            'priority' => '1.0',
        ],
        [
            'loc' => route('klinik.map'),
            'lastmod' => $latestLastmod,
            'changefreq' => 'daily',
            'priority' => '0.9',
        ],
    ];

    $approvedKliniks = Klinik::where('status', 'approved')
        ->orderBy('updated_at', 'desc')
        ->get(['id', 'updated_at']);

    foreach ($approvedKliniks as $klinik) {
        $urls[] = [
            'loc' => route('klinik.show', $klinik),
            'lastmod' => optional($klinik->updated_at)->toDateString(),
            'changefreq' => 'weekly',
            'priority' => '0.8',
        ];
    }

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('sitemap');

Route::get('/klinik/map', [KlinikController::class, 'index'])->name('klinik.map');
Route::get('/klinik/create', [KlinikController::class, 'create'])->name('klinik.create');
Route::post('/klinik', [KlinikController::class, 'store'])->name('klinik.store')->middleware('throttle:klinik-submit');
Route::get('/klinik/{klinik}', [KlinikController::class, 'show'])->name('klinik.show');

// Admin Routes
Route::prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});
