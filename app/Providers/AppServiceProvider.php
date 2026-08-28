<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set custom pagination view
        \Illuminate\Pagination\Paginator::defaultView('custom.pagination');
        \Illuminate\Pagination\Paginator::defaultSimpleView('custom.pagination');

        // ── Rate Limiting (cegah brute-force, spam registrasi, dan banjir upload) ──
        // Login: maks 5 percobaan per menit per IP
        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Registrasi: maks 3 akun per jam per IP
        RateLimiter::for('register', function ($request) {
            return Limit::perMinutes(60, 3)->by($request->ip());
        });

        // Submit klinik (upload foto): maks 2 per jam per IP;
        // user yang sudah login (admin) tidak dibatasi
        RateLimiter::for('klinik-submit', function ($request) {
            if ($request->user()) {
                return Limit::none();
            }
            return Limit::perMinutes(60, 2)->by($request->ip());
        });
    }
}
