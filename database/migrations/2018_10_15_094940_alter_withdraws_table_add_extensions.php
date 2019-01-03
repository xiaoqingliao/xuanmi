<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWithdrawsTableAddExtensions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_withdraws', function(Blueprint $table){
            $table->decimal('fee', 10, 2)->unsigned()->default(0);
            $table->decimal('actual', 10, 2)->unsigned()->default(0);
            $table->decimal('balance', 10, 2)->unsigned()->default(0);
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
            $table->dropColumn('fee');
            $table->dropColumn('actual');
            $table->dropColumn('balance');
        });
    }
}
