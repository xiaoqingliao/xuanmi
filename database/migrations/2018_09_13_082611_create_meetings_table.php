<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->integer('member_id')->unsigned();
            $table->string('title');
            $table->integer('category_id')->unsigned();
            $table->string('cover')->default('');
            $table->text('banners')->nullable();
            $table->string('video')->default('');
            $table->mediumText('content')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamp('start_time')->nullable();
            $table->string('province', 50)->default('');
            $table->string('city', 80)->default('');
            $table->string('area', 80)->default('');
            $table->string('address')->default('');
            $table->string('lat', 10)->default('');
            $table->string('lng', 10)->default('');
            $table->integer('orderindex')->unsigned()->default(0);
            $table->tinyInteger('status')->unsigned()->default(0);
            $table->mediumText('extensions')->nullable();

            $table->index('member_id');
            $table->index('category_id');
            $table->index('start_time');
            $table->index('status');
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
        Schema::dropIfExists('meetings');
    }
}
