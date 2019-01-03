<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberUpgradeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_upgrade_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->integer('old_group_id')->unsigned();
            $table->integer('new_group_id')->unsigned();
            $table->tinyInteger('type')->unsigned();
            $table->integer('userid')->unsigned();

            $table->index('type');
            $table->index('member_id');
            $table->index('old_group_id');
            $table->index('new_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_upgrade_logs');
    }
}
