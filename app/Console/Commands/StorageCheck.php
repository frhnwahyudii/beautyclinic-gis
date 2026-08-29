<?php

namespace App\Console\Commands;

use App\Models\Klinik;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StorageCheck extends Command
{
    protected $signature = 'storage:check {--limit=5}';

    protected $description = 'Diagnosa penyimpanan foto klinik (lokal vs S3) dan tautan URL foto.';

    public function handle(): int
    {
        $this->newLine();
        $this->info('═══ Diagnosa Penyimpanan Foto Klinik ═══');

        // 1. Konfigurasi aktif
        $publicDisk = config('filesystems.public_disk', 'public');
        $fsDisk = config('filesystems.default', 'local');
        $this->line("PUBLIC_DISK (filesystems.public_disk): {$publicDisk}");
        $this->line("FILESYSTEM_DISK (filesystems.default) : {$fsDisk}");

        $diskCfg = config('filesystems.disks.' . $publicDisk, []);
        $driver = $diskCfg['driver'] ?? '?';
        $this->line("Disk '{$publicDisk}' driver: {$driver}");

        if ($driver === 's3') {
            $this->masked('AWS_ACCESS_KEY_ID (key)', $diskCfg['key'] ?? null);
            $this->masked('AWS_SECRET_ACCESS_KEY (secret)', $diskCfg['secret'] ?? null);
            $this->line('AWS_DEFAULT_REGION / AWS_REGION (region): ' . ($diskCfg['region'] ?: '(KOSONG — isi AWS_DEFAULT_REGION!)'));
            $this->line('AWS_BUCKET (bucket): ' . ($diskCfg['bucket'] ?: '(KOSONG — isi AWS_BUCKET!)'));
            $this->line('AWS_URL (url): ' . ($diskCfg['url'] ?: '(tidak diset — fallback ke bucket.region)'));
            $this->line('AWS_ENDPOINT (endpoint): ' . ($diskCfg['endpoint'] ?: '(tidak diset)'));
        }

        // 2. Klinik berfoto
        $total = Klinik::whereNotNull('foto')->count();
        $kliniks = Klinik::whereNotNull('foto')->limit((int) $this->option('limit'))->get();
        $this->newLine();
        $this->info("Klinik dengan foto: {$total} (diperiksa " . $kliniks->count() . ')');

        if ($kliniks->isEmpty()) {
            $this->warn('Tidak ada klinik berfoto di database.');
            return self::SUCCESS;
        }

        // 3. Cek setiap foto
        foreach ($kliniks as $k) {
            $url = $k->foto_url;
            $this->newLine();
            $this->line("• {$k->nama} (foto: {$k->foto})");
            $this->line('  URL: ' . ($url ?: '(NULL — konfigurasi disk tidak lengkap, lihat bagian 1)'));
            if (! $url) {
                continue;
            }

            try {
                $exists = Storage::disk($publicDisk)->exists('klinik_photos/' . $k->foto);
                $this->line('  Objek di bucket/storage: ' . ($exists ? 'ADA' : 'TIDAK ADA — foto belum di-upload ke bucket'));
            } catch (Throwable $e) {
                $this->line('  Objek di bucket/storage: ERROR — ' . $e->getMessage());
            }

            try {
                $status = Http::timeout(10)->get($url)->status();
                $hint = match (true) {
                    $status === 200 => 'OK — foto dapat diakses publik',
                    $status === 403 => '403 — objek/bucket TIDAK publik (buka Block Public Access + bucket policy GetObject)',
                    $status === 404 => '404 — objek tidak ditemukan (upload foto ke bucket)',
                    default => "HTTP {$status}",
                };
                $this->line("  Akses HTTP: {$status} → {$hint}");
            } catch (Throwable $e) {
                $this->line('  Akses HTTP: GAGAL — ' . $e->getMessage());
            }
        }

        // 4. Ringkasan langkah
        $this->newLine();
        $this->info('══════════════════════════════════════════════');
        $this->line('Langkah perbaikan umum:');
        $this->line('  1. Pastikan AWS_DEFAULT_REGION + AWS_BUCKET terisi di env Laravel Cloud.');
        $this->line('  2. Upload foto lama dari komputer lokal:');
        $this->line('     aws s3 cp storage/app/public/klinik_photos/ s3://NAMA-BUCKET/klinik_photos/ --recursive --acl public-read');
        $this->line('  3. Bucket harus bisa dibaca publik: matikan "Block all public access"');
        $this->line('     dan tambahkan bucket policy s3:GetObject pada prefix klinik_photos/*.');
        $this->line('  4. Jika semua baris di atas sudah OK dan status HTTP 200, foto pasti tampil.');

        return self::SUCCESS;
    }

    private function masked(string $label, ?string $value): void
    {
        if (! $value) {
            $this->line("{$label}: (KOSONG)");
            return;
        }
        $shown = strlen($value) > 8 ? substr($value, 0, 4) . '…' . substr($value, -4) : '***';
        $this->line("{$label}: {$shown}");
    }
}
