<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal pembersihan rutin: buang data & foto klinik sampah (anti-bloat storage/database)
Schedule::command('klinik:cleanup')->dailyAt('02:00');
