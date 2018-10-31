<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('user_type',10)->nullable()->change();
            $table->string('gender',6)->nullable()->change();
            $table->string('language')->default('en')->change();
            $table->date('registration_date')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->boolean('status')->default(0)->change();
            $table->boolean('certificate_printed')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
