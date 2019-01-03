<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('title');
            $table->integer('member_id')->unsigned();
            $table->integer('category_id')->unsigned()->default(0);
            $table->string('cover')->default('');
            $table->text('banners')->nullable();
            $table->string('video')->default('');
            $table->mediumText('content')->nullable();
            $table->decimal('price', 10, 2)->unsigned()->default(0);
            $table->integer('orderindex')->unsigned()->default(0);
            $table->mediumText('extensions')->nullable();
            $table->integer('times')->unsigned()->default(0);
            $table->tinyInteger('score')->unsigned()->default(0);
            $table->integer('buy_number')->unsigned()->default(0);
            $table->integer('base_clicks')->unsigned()->default(0);
            $table->integer('clicks')->unsigned()->default(0);
            $table->tinyInteger('status')->unsigned()->default(0);

            $table->index('member_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('price');
            $table->index('orderindex');
            $table->index('score');
            $table->index('buy_number');
            $table->index('times');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
