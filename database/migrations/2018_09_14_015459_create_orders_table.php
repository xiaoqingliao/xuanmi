<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('sn', 40)->unique();
            $table->integer('member_id')->unsigned();
            $table->integer('merchant_id')->unsigned()->default(0);
            $table->tinyInteger('type')->unsigned();
            $table->string('model_type', 20)->default('');
            $table->integer('model_id')->unsigned()->default(0);
            $table->string('title');
            $table->decimal('price', 10, 2)->unsigned()->default(0);
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->text('content')->nullable();
            $table->string('name', 40);
            $table->string('phone', 20);
            $table->text('rebate')->nullable();
            $table->integer('pay_time')->unsigned()->default(0);
            $table->integer('cancel_time')->unsigned()->default(0);
            $table->string('pay_type', 20);
            $table->string('out_trade_no', 50)->default('');
            $table->text('remark')->nullable();
            $table->text('member_remark')->nullable();
            $table->mediumText('extensions')->nullable();

            $table->index('merchant_id');
            $table->index('type');
            $table->index('member_id');
            $table->index('status');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
