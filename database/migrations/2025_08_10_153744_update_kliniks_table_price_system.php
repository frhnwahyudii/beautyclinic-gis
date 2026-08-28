<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kliniks', function (Blueprint $table) {
            // Hapus kolom price_category lama
            $table->dropColumn('price_category');

            // Tambah kolom harga minimum dan maksimum
            $table->integer('min_price')->nullable()->after('status');
            $table->integer('max_price')->nullable()->after('min_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kliniks', function (Blueprint $table) {
            // Kembalikan kolom price_category
            $table->enum('price_category', ['murah', 'menengah', 'mahal'])->nullable()->after('status');

            // Hapus kolom harga
            $table->dropColumn(['min_price', 'max_price']);
        });
    }
};
