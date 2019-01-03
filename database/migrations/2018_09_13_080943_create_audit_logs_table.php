<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->integer('user_id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('model_type', 30);
            $table->integer('model_id')->unsigned();
            $table->tinyInteger('prev_status')->unsigned();
            $table->tinyInteger('new_status')->unsigned();
            $table->string('remark');

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
        Schema::dropIfExists('audit_logs');
    }
}
