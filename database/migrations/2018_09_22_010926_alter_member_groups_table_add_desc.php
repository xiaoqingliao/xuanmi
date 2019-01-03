<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberGroupsTableAddDesc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('member_groups', function(Blueprint $table){
           $table->string('icon', 100)->default('');
           $table->string('price', 40)->default('');
           $table->text('copyright')->nullable();
           $table->text('description')->nullable();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_groups', function(Blueprint $table){
            $table->dropColumn('icon');
            $table->dropColumn('price');
            $table->dropColumn('copyright');
            $table->dropColumn('description');
        });
    }
}
