<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberProxyAppliesTableAddMoney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_proxy_applies', function(Blueprint $table){
            $table->decimal('money', 10, 2)->unsigned();
            $table->text('rebate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_proxy_applies', function(Blueprint $table){
            $table->dropColumn('money');
            $table->dropColumn('rebate');
        });
    }
}
