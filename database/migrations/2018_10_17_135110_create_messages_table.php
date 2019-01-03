<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('sender_id')->unsigned();
            $table->integer('receiver_id')->unsigned();
            $table->text('content')->nullable();
            $table->integer('read_time')->unsigned()->default(0);

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('read_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
