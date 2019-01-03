<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablesRemoveScore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function(Blueprint $table){
            $table->dropColumn('score');
            $table->integer('scores')->unsigned()->default(0);
            $table->integer('score_count')->unsigned()->default(0);

            $table->index('scores');
        });

        Schema::table('meetings', function(Blueprint $table){
            $table->dropColumn('score');
            $table->integer('scores')->unsigned()->default(0);
            $table->integer('score_count')->unsigned()->default(0);
            $table->index('scores');
        });
        
        Schema::table('courses', function(Blueprint $table){
            $table->dropColumn('score');
            $table->integer('scores')->unsigned()->default(0);
            $table->integer('score_count')->unsigned()->default(0);
            $table->index('scores');
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
            $table->integer('score')->unsigned()->default(0);
            $table->dropColumn('scores');
            $table->dropColumn('score_count');
        });

        Schema::table('meetings', function(Blueprint $table){
            $table->integer('score')->unsigned()->default(0);
            $table->dropColumn('scores');
            $table->dropColumn('score_count');
        });
        
        Schema::table('courses', function(Blueprint $table){
            $table->integer('score')->unsigned()->default(0);
            $table->dropColumn('scores');
            $table->dropColumn('score_count');
        });
    }
}
