<?php

namespace App\Console\Commands;

use App\Models\Klinik;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupKlinikData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'klinik:cleanup
        {--days-rejected=7 : Hapus klinik berstatus ditolak yang lebih tua dari N hari}
        {--days-pending=30 : Hapus klinik pending yang belum ditinjau lebih dari N hari}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data & foto klinik yang ditolak/kedaluwarsa untuk mencegah pembengkakan storage dan database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysRejected = (int) $this->option('days-rejected');
        $daysPending = (int) $this->option('days-pending');

        // 1. Hapus klinik berstatus "rejected" yang sudah tua (beserta fotonya)
        $rejectedCutoff = now()->subDays($daysRejected);
        $rejected = Klinik::where('status', 'rejected')
            ->where('created_at', '<', $rejectedCutoff)
            ->get();

        foreach ($rejected as $klinik) {
            if ($klinik->foto) {
                Storage::delete('public/klinik_photos/' . $klinik->foto);
            }
            $klinik->delete();
        }
        $this->info("Klinik ditolak (>{$daysRejected} hari) dihapus: {$rejected->count()}");

        // 2. Hapus klinik "pending" yang kedaluwarsa (tidak pernah ditinjau)
        $pendingCutoff = now()->subDays($daysPending);
        $pending = Klinik::where('status', 'pending')
            ->where('created_at', '<', $pendingCutoff)
            ->get();

        foreach ($pending as $klinik) {
            if ($klinik->foto) {
                Storage::delete('public/klinik_photos/' . $klinik->foto);
            }
            $klinik->delete();
        }
        $this->info("Klinik pending kedaluwarsa (>{$daysPending} hari) dihapus: {$pending->count()}");

        // 3. Hapus foto "yatim" — file di storage tanpa data klinik di database
        $orphaned = 0;
        $files = Storage::files('public/klinik_photos');
        foreach ($files as $file) {
            $fileName = basename($file);
            if (! Klinik::where('foto', $fileName)->exists()) {
                Storage::delete($file);
                $orphaned++;
            }
        }
        $this->info("Foto yatim dihapus: {$orphaned}");

        return self::SUCCESS;
    }
}
