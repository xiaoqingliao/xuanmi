<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMeetingsTableAddClicks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function(Blueprint $table){
            $table->integer('clicks')->unsigned()->default(0);
            $table->integer('buy_number')->unsigned()->default(0);
            $table->integer('score')->unsigned()->default(0);
            $table->integer('base_clicks')->unsigned()->default(0);

            $table->index('score');
            $table->index('buy_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function(Blueprint $table){
            $table->dropColumn('clicks');
            $table->dropColumn('buy_number');
            $table->dropColumn('score');
            $table->dropColumn('base_clicks');
        });
    }
}
