<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCoursesTableAddPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function(Blueprint $table){
            $table->decimal('market_price', 10, 2)->unsigned();
            $table->integer('discount_end_time')->unsigned()->default(0);
            $table->integer('discount_start_time')->unsigned()->default(0);
            $table->string('relate_courses')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function(Blueprint $table){
            $table->dropColumn('market_price');
            $table->dropColumn('discount_start_time');
            $table->dropColumn('discount_end_time');
            $table->dropColumn('relate_courses');
        });
    }
}
