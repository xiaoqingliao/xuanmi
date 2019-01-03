<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLoginRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_login_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->string('ip', 30)->default('');
            $table->string('areas')->default('');

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
        Schema::dropIfExists('member_login_records');
    }
}
