<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('generalsettings')->insert([
            'date_format' => 'DD-MM-YYYY',
            'timings' => '00-24'
        ]);
    }
}
