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

        // FACILITIES
        DB::table('gym_facilities')->truncate();
        DB::table('facilities')->truncate();
        for ($i = 1; $i <= 6; $i++) {
            factory(\App\Facilitie::class)->create(["icon" => "f{$i}.svg"]);
        }
        // #FACILITIES

        // ACTIVITIES
        DB::table('gym_activities')->truncate();
        DB::table('activities')->truncate();
        for ($i = 1; $i <= 3; $i++) {
            factory(\App\Activitie::class)->create(["icon" => "a{$i}.svg"]);
        }
        // #ACTIVITIES


        DB::table('gyms')->truncate();
        DB::table('gym_subscription_types')->truncate();
        DB::table('addresses')->where("addressable_type",  "App\\Gym")->delete();

        $i = 0;
        factory(\App\Gym::class, 17)
            ->create()
            ->each(function ($gym) use (&$i) {
                $i++;
                $address = [
                    ["latitude"=> 33.61416799, "longitude" => -7.55374872],
                    ["latitude"=> 33.6085969, "longitude" => -7.6244165 ],
                    ["latitude"=> 33.59574833, "longitude" => -7.59766488],
                    ["latitude" => 33.576413, "longitude" => -7.619501],
                    ["latitude" => 33.581275, "longitude" => -7.581703],
                    ["latitude" => 33.564685, "longitude" => -7.622937],
                    ["latitude" => 33.584708, "longitude" => -7.661421],
                    ["latitude" => 33.572122, "longitude" => -7.661765],
                    ["latitude" => 33.553527, "longitude" => -7.526382],
                    ["latitude" => 33.542081, "longitude" => -7.620188],
                    ["latitude" => 33.556388, "longitude" => -7.665888],
                    ["latitude" => 33.578129, "longitude" => -7.570020],
                    ["latitude" => 33.590714, "longitude" => -7.527413],
                    ["latitude" => 33.625598, "longitude" => -7.494082],
                    ["latitude" => 33.559764, "longitude" => -7.709883],
                    ["latitude" => 33.552096, "longitude" => -7.680631],
                    ["latitude" => 33.529204, "longitude" => -7.611909],
                ];

                // Addresses
                $gym->address()->save(factory(App\Address::class)->make($address[$i] ?? $address[0]));

                // Prices
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

                // Facilities
                $facilities = \App\Facilitie::inRandomOrder()->limit($this->faker->numberBetween(5, 6))->get();
                $gym->facilities()->saveMany($facilities);

                // Activities
                $activities = \App\Activitie::inRandomOrder()->limit($this->faker->numberBetween(1, 4))->get();
                $gym->activities()->saveMany($activities);

            });
    }
}
