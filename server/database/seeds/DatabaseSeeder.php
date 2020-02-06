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

       factory(\App\Account::class, 50)
           ->create()
           ->each(function ($account) {
               AccountService::assignRole($account);
               $x = $account->accountable()->first();
               $x->address()->save(factory(App\Address::class)->make());
           });


        // Settings
       \App\Setting::insert(["key" => "sponsorship-rate", "value" => 0]);
       \App\Setting::insert(["key" => "ambassador-sponsorship-rate", "value" => 10]);


       // Fake user
       \App\Account::where("accountable_type", "App\\Admin")->first()->update(["username" => "@verify", "disabled" => 0]);
       \App\Account::where("accountable_type", "App\\Customer")->first()->update(["email" => "s1@spotfit.ma", "disabled" => 0]);
    }
}
