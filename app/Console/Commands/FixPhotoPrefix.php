<?php

namespace App\Console\Commands;

use App\Models\Klinik;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FixPhotoPrefix extends Command
{
    protected $signature = 'storage:fix-prefix';

    protected $description = 'Menyalin foto klinik dari root bucket ke folder klinik_photos/ (memperbaiki foto yang 404).';

    public function handle(): int
    {
        $diskName = config('filesystems.public_disk', 'public');
        $this->info("Disk aktif: {$diskName}");

        $kliniks = Klinik::whereNotNull('foto')->get();
        if ($kliniks->isEmpty()) {
            $this->warn('Tidak ada klinik berfoto di database.');
            return self::SUCCESS;
        }

        $disk = Storage::disk($diskName);
        $ok = $copied = $missing = 0;

        foreach ($kliniks as $k) {
            $target = 'klinik_photos/' . $k->foto;
            try {
                if ($disk->exists($target)) {
                    $ok++;
                    continue;
                }

                if ($disk->exists($k->foto)) {
                    // Objek ada di root bucket (bukan di klinik_photos/) — salin ke prefix yang benar.
                    $disk->copy($k->foto, $target);
                    try {
                        $disk->setVisibility($target, 'public');
                    } catch (Throwable $ignored) {
                        // Abaikan bila bucket memakai bucket-policy (bukan ACL).
                    }
                    $copied++;
                    $this->line("  [SALIN] {$k->nama}: {$k->foto}");
                } else {
                    $missing++;
                    $this->warn("  [TIDAK ADA] {$k->nama}: {$k->foto} (tidak ditemukan di root maupun klinik_photos/)");
                }
            } catch (Throwable $e) {
                $missing++;
                $this->error("  [ERROR] {$k->nama}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Selesai — sudah benar: {$ok}, disalin: {$copied}, tidak ditemukan: {$missing}");
        $this->line('Muat ulang halaman map/detail (hard refresh: Ctrl+F5) untuk melihat foto.');

        return self::SUCCESS;
    }
}
