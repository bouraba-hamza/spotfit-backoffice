<?php

use Illuminate\Database\Seeder;
use \App\Services\AccountService;
use Faker\Generator as Faker;
use \Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
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
        DB::table('accounts')->truncate();

        DB::table('admins')->truncate();
        DB::table('partners')->truncate();
        DB::table('customers')->truncate();
        DB::table('trainers')->truncate();
        DB::table('supervisors')->truncate();

        DB::table('addresses')->whereIn("addressable_type",
            [
                "App\\Admin",
                "App\\Partner",
                "App\\Customer",
                "App\\Trainer",
                "App\\Supervisor",
            ]
        )->delete();

        DB::table('customer_subscription')->truncate();
        DB::table('sessions')->truncate();
        DB::table('statuses')->truncate();
        DB::table('customer_subscription_statuses')->truncate();
        DB::table('gym_subscription_types')->truncate();


        $statuses = [
            ["name" => "reserved", "color" => $this->faker->hexColor],
            ["name" => "confirmed", "color" => $this->faker->hexColor],
            ["name" => "expired", "color" => $this->faker->hexColor],
            ["name" => "canceled", "color" => $this->faker->hexColor],
            ["name" => "activated", "color" => $this->faker->hexColor],
        ];


        DB::table('statuses')->insert($statuses);

        factory(\App\Account::class, 50)
            ->create()
            ->each(function ($account) {
                AccountService::assignRole($account);
                $person = $account->accountable()->first();
                $person->address()->save(factory(App\Address::class)->make());

                if(AccountService::customer($person)) {

                    $t1 = rand(1, 15);
                    for ($i = 0; $i < $t1; $i++) {
                        // GYM SUBSCRIPTION TYPE
                        $gst = DB::table('gym_subscription_types')->insertGetId($this->getGymSubscriptionTypeRecord());


                        // SUBSCRIPTIONS
                        $fakeSubscription = [
                            "gym_subscription_type" => $gst,
                            "price" => $this->faker->numberBetween(40, 700),
                            "qrcode" => $this->faker->uuid,
                            "payment_method_id" => 1,
                            "consumed_at" => $this->faker->dateTime,
                            "remaining_sessions" => rand(0, 31),
                            "customer_id" => $person->id,
                            "created_at" => now(),
                            "updated_at" => now(),
                        ];

                        $subscriptionId = DB::table('customer_subscription')->insertGetId($fakeSubscription);

                        // SESSIONS
                        $sessions = $this->getRandomSessions(0, 10, $subscriptionId);
                        DB::table('sessions')->insert($sessions);

                        // STATUS
                        $statuses = $this->getRandomStatuses(rand(1, 3));
                        foreach ($statuses as $s) {
                            \App\CustomerSubscription::find($subscriptionId)->statuses()->attach($s->id, ['datetime' => $this->faker->dateTime]);
                        }

                    }
                }
            });


        // Fake user
        \App\Account::where("accountable_type", "App\\Admin")->first()->update(["username" => "@verify", "disabled" => 0]);
        \App\Account::where("accountable_type", "App\\Customer")->first()->update(["email" => "s1@spotfit.ma", "disabled" => 0]);
    }

    private function getRandomSessions(int $min, int $max,  int $customer_subscription_id) {
        $times = rand($min, $max);
        $sessions = [];
        for ($i = 0; $i < $times; $i++) {
            $fakeSession = [
                "qrcode" => $this->faker->uuid,
                "gym_id" => App\Gym::inRandomOrder()->first()->id,
                "customer_subscription_id" => $customer_subscription_id,
                "date" => $this->faker->dateTime,
            ];

            array_push($sessions, $fakeSession);
        }
        return $sessions;
    }

    public function getRandomStatuses(int $howMany = 1) {
        return App\Status::inRandomOrder()->take($howMany)->get();
    }

    public function getRandomGym(int $howMany = 1) {
        return App\Gym::inRandomOrder()->take($howMany)->get();
    }

    public function getRandomSubscription(int $howMany = 1) {
        return App\Subscription::inRandomOrder()->take($howMany)->get();
    }

    public function getRandomType(int $howMany = 1) {
        return App\Type::inRandomOrder()->take($howMany)->get();
    }

    public function getGymSubscriptionTypeRecord() {
        return $gym_subscription_types = [
            "gym_id" => $this->getRandomGym()[0]->id,
            "subscription_id" => $this->getRandomSubscription()[0]->id,
            "type_id" => $this->getRandomType()[0]->id,
            "price" => $this->faker->numberBetween(40, 700),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
