<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberRenewLogsTableAddSnapshot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_renew_logs', function(Blueprint $table){
            $table->text('snapshot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_renew_logs', function(Blueprint $table){
            $table->dropColumn('snapshot');
        });
    }
}
