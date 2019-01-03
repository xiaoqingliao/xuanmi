<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->string('model_type', 10);
            $table->integer('model_id')->unsigned();
            $table->string('content');
            $table->tinyInteger('score')->unsigned();
            
            $table->index('member_id');
            $table->index(['model_type', 'model_id']);
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
        Schema::dropIfExists('rates');
    }
}
