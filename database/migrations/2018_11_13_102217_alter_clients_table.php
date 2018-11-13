<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('location');

            $table->string('registration_number',10)->nullable()->after('registration_date');
            $table->string('address')->nullable()->after('registration_number');
            $table->string('region')->nullable()->after('address');
            $table->string('district')->nullable()->after('region');
            $table->string('ward')->nullable()->after('district');
            $table->string('zipcode')->nullable()->after('ward');
            
            $table->boolean('exempt')->default(0)->after('zipcode');
            $table->string('tax_type')->nullable()->after('exempt');
            $table->string('filling_type')->nullable()->after('tax_type');
            $table->string('filling_period')->nullable()->after('filling_type');
            $table->string('filling_currency')->nullable()->after('filling_period');
            $table->date('due_date')->nullable()->after('filling_currency');
            $table->float('total_amount',10,2)->nullable()->after('due_date');
            $table->float('penalty_amount',10,2)->nullable()->after('total_amount');

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
