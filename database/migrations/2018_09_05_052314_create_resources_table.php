<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title');
            $table->string('path');
            $table->string('source_path')->default('');
            $table->integer('size')->undefined()->default(0);
            $table->string('category', 20);
            $table->string('tag', 30)->default('');
            $table->string('cover')->default('');
            $table->smallInteger('width')->unsigned()->default(0);
            $table->smallInteger('height')->unsigned()->default(0);
            $table->smallInteger('duration')->unsigned()->default(0);
            $table->string('mimetype')->default('');
            $table->enum('status', ['pending', 'normal', 'processing', 'disabled']);
            
            $table->index('category');
            $table->index('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }
}
