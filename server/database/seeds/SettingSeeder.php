<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->truncate();
        // Settings
        \App\Setting::insert(["key" => "sponsorship-rate", "value" => 0]);
        \App\Setting::insert(["key" => "ambassador-sponsorship-rate", "value" => 10]);
        \App\Setting::insert(["key" => "spotfit_everywhere_pass", "value" => json_encode([
            "silver" => 359.99,
            "gold" => 750.99,
            "platinum" => 1500,
        ])]);
    }
}
