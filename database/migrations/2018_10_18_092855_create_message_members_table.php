<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_members', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->integer('visit_member_id')->unsigned();
            $table->text('content')->nullable();
            $table->integer('unread_count')->unsigned()->default(0);

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
        Schema::dropIfExists('message_members');
    }
}
