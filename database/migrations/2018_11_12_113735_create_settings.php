<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('generalsettings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date_format')->default('d/m/Y');
            $table->string('time_from')->after('date_format')->default('01');
            $table->string('time_to')->after('time_from')->default('24');
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
