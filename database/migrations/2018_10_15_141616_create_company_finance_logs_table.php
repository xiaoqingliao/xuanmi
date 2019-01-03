<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyFinanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_finance_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('member_id')->unsigned();
            $table->decimal('price', 20, 2)->unsigned()->default(0);
            $table->text('snapshot')->nullable();

            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_finance_logs');
    }
}
