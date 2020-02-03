<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class GymsSeeder extends Seeder
{
    private $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CLASSES
        DB::table('classes')->truncate();
        $classes = [
            ["name" => "Platinum"],
            ["name" => "Gold"],
            ["name" => "Silver"],
        ];

        \App\Classe::insert($classes);
        // #CLASSES

        // SUBSCRIPTIONS
        DB::table('subscriptions')->truncate();
        $subscriptions = [
            ["name" => "Day Pass", "duration" => 1, "description" => $this->faker->text(70)],
            ["name" => "Year Pass", "duration" => 365, "description" => $this->faker->text(70)],
            ["name" => "Month Pass", "duration" => 30, "description" => $this->faker->text(70)],
            ["name" => "Week Pass", "duration" => 7, "description" => $this->faker->text(70)],
        ];

        \App\Subscription::insert($subscriptions);
        // #SUBSCRIPTIONS

        // TYPES
        DB::table('types')->truncate();
        $types = [
            ["name" => "strict"],
            ["name" => "everywhere"],
        ];

        \App\Type::insert($types);
        // #TYPES


        DB::table('gyms')->truncate();
        DB::table('gym_subscription_types')->truncate();

        factory(\App\Gym::class, 5)
            ->create()
            ->each(function ($gym) {
                $gym->address()->save(factory(App\Address::class)->make());

                \App\Type::all()->each(function ($type) use ($gym) {
                    switch ($type->name) {
                        case 'strict':
                            \App\Subscription::all()->each(function ($subscription) use ($gym, $type) {
                                \App\GymSubscriptionType::create([
                                    "gym_id" => $gym->id,
                                    "subscription_id" => $subscription->id,
                                    "type_id" => $type->id,
                                    "price" => $this->faker->numberBetween(40, 700)
                                ]);
                            });
                            break;
                        case 'everywhere':
                            \App\Subscription::where('duration', 30)->each(function ($subscription) use ($gym, $type) {
                                \App\GymSubscriptionType::create([
                                    "gym_id" => $gym->id,
                                    "subscription_id" => $subscription->id,
                                    "type_id" => $type->id,
                                    "price" => $this->faker->numberBetween(40, 700)
                                ]);
                            });
                            break;
                    }
                });


            });
    }
}
