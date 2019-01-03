<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMembersTableAddRenew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function(Blueprint $table){
            $table->decimal('renew_cash', 10, 2)->unsigned()->default(0);
            $table->decimal('renew_withdraw', 10, 2)->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function(Blueprint $table){
            $table->dropColumn('renew_cash');
            $table->dropColumn('renew_withdraw');
        });
    }
}
