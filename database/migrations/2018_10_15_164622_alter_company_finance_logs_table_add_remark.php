<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyFinanceLogsTableAddRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_finance_logs', function(Blueprint $table){
            $table->text('remark')->nullable();
            $table->string('model', 40)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_finance_logs', function(Blueprint $table){
            $table->dropColumn('remark');
            $table->dropColumn('model');
        });
    }
}
