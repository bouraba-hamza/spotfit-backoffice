<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Activitie;
use Faker\Generator as Faker;

$factory->define(Activitie::class, function (Faker $faker) {
    $activities = [];

    for ($i = 1; $i < 3; $i++) {
        array_push($activities, "a{$i}.svg");
    }

    return [
        "icon" => $faker->randomElement($activities),
        "name" => $faker->word,
        "order" => $faker->numberBetween(0, 100),
    ];
});
