<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Smsscheduletypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smsscheduletypes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',350);
            $table->string('username');
            $table->string('password');
            $table->string('apiurl',500);
            $table->integer('frequency');
            $table->text('en_smsbody');
            $table->boolean('status')->default(0);
            $table->dateTime('lastrundatetime')->nullable();
            $table->integer('lastrunsms')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
