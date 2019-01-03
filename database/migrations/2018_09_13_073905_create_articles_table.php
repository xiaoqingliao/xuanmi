<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('title');
            $table->integer('category_id')->unsigned();
            $table->integer('member_id')->unsigned()->default(0);
            $table->string('cover')->default('');
            $table->string('video')->default('');
            $table->text('banners')->nullable();
            $table->mediumText('content')->nullable();
            $table->mediumText('extensions')->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);
            $table->integer('orderindex')->unsigned()->default(0);

            $table->index('category_id');
            $table->index('member_id');
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
        Schema::dropIfExists('articles');
    }
}
