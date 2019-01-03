<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberAccountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_account_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->integer('member_id')->unsigned();
            $table->tinyInteger('type')->unsigned();
            $table->tinyInteger('category')->unsigned();
            $table->decimal('cash', 10, 2)->unsigned();
            $table->text('remark')->nullable();

            $table->index('member_id');
            $table->index('type');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_account_logs');
    }
}
