<?php

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
        $this->call([SettingSeeder::class]);
    }
}
