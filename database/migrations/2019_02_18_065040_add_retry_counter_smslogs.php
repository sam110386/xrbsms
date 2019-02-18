<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRetryCounterSmslogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smslogs', function (Blueprint $table) {
            $table->integer('status')->default(0)->change();
            $table->integer('retry_count')->default(0)->after('type');
            $table->string('message_id')->after('id');
            $table->string('error',500)->nullable()->after('retry_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smslogs', function (Blueprint $table) {
            //
        });
    }
}
