<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberWithdrawsTableAddRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_withdraws', function(Blueprint $table){
            $table->integer('userid')->unsigned()->default(0);
            $table->text('remark')->nullable();
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
            $table->dropColumn('userid');
            $table->dropColumn('remark');
        });
    }
}
