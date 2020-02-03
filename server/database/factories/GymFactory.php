<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Gym;
use Faker\Generator as Faker;

$factory->define(Gym::class, function (Faker $faker) {
    $logos = [
        "https://image.freepik.com/vecteurs-libre/vecteur-prime-fitness-gym-logo_144543-140.jpg",
        "https://cms-assets.tutsplus.com/uploads/users/151/posts/32516/image/Placeit4.jpg",
        "https://i.pinimg.com/originals/85/c8/bd/85c8bd2cb128127e2e152663bf97a01a.jpg",
        "https://15logo.net/wp-content/uploads/2017/02/Gym-Studio-800x800.jpg",
    ];

    $gymName = $faker->words(3, true);
    return [
        "group_id" => function () use ($gymName) {
            return factory(App\Group::class)->create(['name' => $gymName,])->id;
        },
        "logo" => $faker->randomElement($logos),
        "name" => $gymName,
        "rate" => $faker->numberBetween(1, 5),
        "qrcode" => $faker->uuid,
        "facilities" => json_encode([]),
        "planning" => json_encode([]),
        "class_id" => function () {
            return App\Classe::inRandomOrder()->first("id")->id;
        },

    ];
});
