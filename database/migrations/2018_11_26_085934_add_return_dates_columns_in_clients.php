<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReturnDatesColumnsInClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('taxcategory');
            
            $table->boolean('returns_opt')->default(0)->after('tax_type');
            $table->date('return_due_date')->nullable()->after('returns_opt');

            $table->boolean('motor_vehicle_opt')->default(0)->after('return_due_date');
            $table->date('motor_vehicle_due_date')->nullable()->after('motor_vehicle_opt');
            
            $table->boolean('driving_licence_opt')->default(0)->after('motor_vehicle_due_date');
            $table->date('driving_licence_due_date')->nullable()->after('driving_licence_opt');

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
