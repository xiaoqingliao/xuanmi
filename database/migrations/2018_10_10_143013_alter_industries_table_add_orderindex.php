<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndustriesTableAddOrderindex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('industries', function(Blueprint $table){
            $table->integer('orderindex')->unsigned()->default(0);

            $table->index('orderindex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('industries', function(Blueprint $table){
            $table->dropColumn('orderindex');
        });
    }
}
