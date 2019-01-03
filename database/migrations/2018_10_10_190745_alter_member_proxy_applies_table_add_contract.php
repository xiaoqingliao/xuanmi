<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberProxyAppliesTableAddContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_proxy_applies', function(Blueprint $table){
            $table->string('contract', 100)->default('');
            $table->string('bank', 100)->default('');
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
            $table->dropColumn('contract');
            $table->dropColumn('bank');
        });
    }
}
