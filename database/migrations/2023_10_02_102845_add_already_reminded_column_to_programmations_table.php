<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlreadyRemindedColumnToProgrammationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programmations', function (Blueprint $table) {
            $table->boolean('already_reminded')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programmations', function (Blueprint $table) {
            $table->dropColumn('already_reminded');
        });
    }
}
