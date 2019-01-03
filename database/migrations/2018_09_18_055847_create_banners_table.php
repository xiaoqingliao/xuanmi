<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->string('model_type', 20);
            $table->integer('model_id')->unsigned()->default(0);
            $table->string('title')->default('');
            $table->string('cover')->default('');
            $table->string('video')->default('');
            $table->string('redirect')->default('');
            $table->integer('orderindex')->unsigned()->default(0);

            $table->index('member_id');
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
        Schema::dropIfExists('banners');
    }
}
