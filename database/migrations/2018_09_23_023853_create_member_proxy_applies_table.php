<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberProxyAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_proxy_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->integer('status')->unsigned();

            $table->index('member_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_proxy_applies');
    }
}
