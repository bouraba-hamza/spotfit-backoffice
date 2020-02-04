<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facilitie;
use Faker\Generator as Faker;

$factory->define(Facilitie::class, function (Faker $faker) {
    $facilities = [];

    for ($i = 1; $i < 7; $i++) {
        array_push($facilities, "f{$i}.svg");
    }

    return [
        "icon" => $faker->randomElement($facilities),
        "name" => $faker->word,
        "order" => $faker->numberBetween(0, 100),
    ];
});
