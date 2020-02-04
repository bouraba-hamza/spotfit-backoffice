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
        factory(\App\Gym::class, 20)
            ->create()
            ->each(function ($gym) use (&$i) {
                $i++;
                $address = [
                    ["latitude"=> 33.61416799, "longitude" => -7.55374872],
                    ["latitude"=> 33.61253798, "longitude" => -7.59840514],
                    ["latitude"=> 33.6085969, "longitude" => -7.6244165 ],
                    ["latitude"=> 33.61416799, "longitude" => -7.55374872 ],
                    ["latitude"=> 33.60592533, "longitude" => -7.62283524],
                    ["latitude"=> 33.59574833, "longitude" => -7.59766488],
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
