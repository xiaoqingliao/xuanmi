<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterArticlesTableAddClicks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function(Blueprint $table){
            $table->integer('base_clicks')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->integer('score')->unsigned()->default(0);

            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function(Blueprint $table){
            $table->dropColumn('base_clicks');
            $table->dropColumn('clicks');
            $table->dropColumn('score');
        });
    }
}
