<?php

use App\Services\AccountService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Calling other seeders
       $this->call([RolesAndPermissionsSeeder::class]);
       $this->call([GymsSeeder::class]);
       $this->call([AccountSeeder::class]);


        // Settings
       \App\Setting::insert(["key" => "sponsorship-rate", "value" => 0]);
       \App\Setting::insert(["key" => "ambassador-sponsorship-rate", "value" => 10]);
    }
}
