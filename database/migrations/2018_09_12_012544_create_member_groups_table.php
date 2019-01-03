<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('title', 100);
            $table->string('code', 40)->unique();
            $table->text('params')->nullable();
            $table->integer('orderindex')->unsigned()->default(0);
            $table->tinyInteger('same')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_groups');
    }
}
