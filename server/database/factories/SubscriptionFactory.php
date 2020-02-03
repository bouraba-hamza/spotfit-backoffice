<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Subscription::class, function (Faker $faker) {
    $subscriptions = [
        ["name" => "Day Pass", "duration" => 1],
        ["name" => "Year Pass", "duration" => 365],
        ["name" => "Month Pass", "duration" => 30],
        ["name" => "Week Pass", "duration" => 7],
    ];

    $sub = $faker->randomElement($subscriptions);
    return [
        'name' => $sub["name"],
        'description' => $faker->text(200),
        'duration' => $sub["duration"],
    ];
});
