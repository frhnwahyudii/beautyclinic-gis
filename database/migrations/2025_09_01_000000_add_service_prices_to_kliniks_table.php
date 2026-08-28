<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicePricesToKliniksTable extends Migration
{
    public function up()
    {
        Schema::table('kliniks', function (Blueprint $table) {
            $table->json('service_prices')->nullable()->after('services');
        });
    }

    public function down()
    {
        Schema::table('kliniks', function (Blueprint $table) {
            $table->dropColumn('service_prices');
        });
    }
}
