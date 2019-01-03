<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMeetingsTableAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function(Blueprint $table){
            $table->timestamp('end_time')->nullable();
            $table->string('sponor_ids')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function(Blueprint $table){
            $table->dropColumn('end_time');
            $table->dropColumn('sponor_ids');
        });
    }
}
