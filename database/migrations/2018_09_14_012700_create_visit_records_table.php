<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->string('model_type', 20);
            $table->integer('model_id')->unsigned();
            $table->string('title')->default('');
            $table->string('ip', 30)->default('');
            $table->string('areas')->default('');
            $table->string('device', 100)->default('');
            $table->string('agent')->default('');

            $table->index(['model_type', 'model_id']);
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
        Schema::dropIfExists('visit_records');
    }
}
