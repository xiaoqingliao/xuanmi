<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('order_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('model_type', 20);
            $table->integer('model_id')->unsigned();
            $table->integer('sku_id')->unsigned()->default(0);
            $table->decimal('price', 10, 2)->unsigned();
            $table->integer('number')->unsigned();
            $table->mediumText('snapshot')->nullable();

            $table->index('order_id');
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
