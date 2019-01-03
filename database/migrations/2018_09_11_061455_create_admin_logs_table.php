<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_logs', function(Blueprint $table){
            $table->increments('id');
            $table->integer('adminid')->unsigned()->default(0);
            $table->string('operation', 20)->default('');
            $table->mediumText('content')->nullable();
            $table->mediumText('summary')->nullable();
            $table->text('desc')->nullable();
            $table->string('ip', 30)->default('');
            $table->string('areas')->nullable();

            $table->index('adminid', 'idx_admin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_logs');
    }
}
