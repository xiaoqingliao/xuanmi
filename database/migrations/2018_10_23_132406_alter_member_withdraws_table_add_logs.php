<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberWithdrawsTableAddLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_withdraws', function(Blueprint $table){
            $table->string('logs')->default('');
            $table->string('type', 20)->default('');
            $table->string('account')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_withdraws', function(Blueprint $table){
            $table->dropColumn('logs');
            $table->dropColumn('type');
            $table->dropColumn('account');
        });
    }
}
