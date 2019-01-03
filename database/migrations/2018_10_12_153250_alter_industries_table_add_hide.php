<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndustriesTableAddHide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('industries', function(Blueprint $table){
            $table->tinyInteger('hide')->unsigned()->default(0);
            $table->index('hide');
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
            $table->dropColumn('hide');
        });
    }
}
