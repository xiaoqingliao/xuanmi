<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('openid', 40)->unique();
            $table->string('nickname', 50);
            $table->string('name', 50)->default('');
            $table->string('avatar', 100)->default('');
            $table->string('avatar_source')->default('');
            $table->tinyInteger('gender')->unsigned()->default(0);
            $table->integer('group_id')->unsigned()->default(0);
            $table->integer('parent_id')->unsigned()->default(0);
            $table->string('parent_path')->default('');
            $table->string('company')->default('');
            $table->string('duty', 50)->default('');
            $table->string('phone', 20)->default('');
            $table->string('wechat', 20)->default('');
            $table->text('summary')->nullable();
            $table->text('userinfo')->nullable();
            $table->text('extensions')->nullable();
            $table->tinyInteger('logged')->unsigned()->default(0);
            $table->integer('last_login')->unsigned()->default(0);
            $table->string('last_ip', 30)->nullable();
            $table->string('last_area', 100)->nullable();
            $table->integer('last_buy')->unsigned()->default(0);

            $table->integer('proxy_first_time')->unsigned()->default(0);
            $table->integer('proxy_start_time')->unsigned()->default(0);
            $table->integer('proxy_end_time')->unsigned()->default(0);
            $table->decimal('proxy_balance', 10, 2)->unsigned()->default(0);

            $table->index('group_id');
            $table->index('parent_id');
            $table->index('last_login');
            $table->index('last_buy');

            $table->index('proxy_start_time');
            $table->index('proxy_end_time');
            $table->index('proxy_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
