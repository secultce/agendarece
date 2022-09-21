<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdColumnToProgrammationLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programmation_links', function (Blueprint $table) {
            $table
                ->foreignId('user_id')
                ->constrained()
                ->after('id')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programmation_links', function (Blueprint $table) {
            $table->dropForeign('programmation_links_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
