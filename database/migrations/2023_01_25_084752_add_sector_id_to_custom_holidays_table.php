<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectorIdToCustomHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_holidays', function (Blueprint $table) {
            $table->foreignId('sector_id')->nullable()->constrained()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_holidays', function (Blueprint $table) {
            $table->dropForeign('logs_sector_id_foreign');
            $table->dropColumn('sector_id');
        });
    }
}
