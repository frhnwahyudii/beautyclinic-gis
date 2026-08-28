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
            $table->text('deskripsi')->nullable()->after('jam_operasional');
            $table->enum('price_category', ['murah', 'menengah', 'mahal'])->default('menengah')->after('deskripsi');
            $table->json('services')->nullable()->after('price_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kliniks', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'price_category', 'services']);
        });
    }
};
