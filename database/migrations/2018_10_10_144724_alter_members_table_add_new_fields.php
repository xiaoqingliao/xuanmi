<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMembersTableAddNewFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members', function(Blueprint $table){
            $table->string('industry')->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('area', 100)->nullable();
            $table->string('address')->nullable();
            $table->string('nation_province', 100)->nullable();
            $table->string('nation_city', 100)->nullable();
            $table->string('nation_area', 100)->nullable();
            $table->string('nation_address')->nullable();
            $table->string('school')->nullable();
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
            $table->dropColumn('industry');
            $table->dropColumn('province');
            $table->dropColumn('city');
            $table->dropColumn('area');
            $table->dropColumn('address');
            $table->dropColumn('school');
            $table->dropColumn('nation_province');
            $table->dropColumn('nation_city');
            $table->dropColumn('nation_area');
            $table->dropColumn('nation_address');
        });
    }
}
