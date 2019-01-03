<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMembersAddBankinfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function(Blueprint $table){
            $table->string('bank_name')->nullable();
            $table->string('bank_no')->nullable();
            $table->string('bank_contact')->nullable();
            $table->string('alipay')->nullable();
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
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_no');
            $table->dropColumn('bank_contact');
            $table->dropColumn('alipay');
        });
    }
}
