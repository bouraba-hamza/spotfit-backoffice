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
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQ4SwrCzbFtcotxpREmtMEqLcALfc_Ge4yG_P0WIP-XNd0e6iLR",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQ3PPl9uIcPOjKsUTlxOunOUNBfku_oU6bw6HXFaf8LLJQjc75y",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcT8eTYgbP_rSrJSxQJ-8TzoseXEqf1YbigVR0Dw-7jdIOrH8Nui",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQd9t40fGepC0HHx78STXsq4YMVQCbSUKOUzerGPvJDNj2kjYQi",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQjwY28OuyrPq8GLSHNuGJp4NnKWPS70mHLhuG31HIBnMm9e6YH",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTGpSa3X53hPLY1gC_4bQgRqsqz692w8M96ilKWBmPqXZ_Li76a",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQFPrTilyT1oul-4ALIPoAJt0EOxjeiLZINE0PhvnvrpzXO5Tqr",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcQssBbpafe1WK9MQmJzH983NhQgP8zQoq_Mt7PAg8aZqv0sDW9q",
        "https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcReCUvmIiGX_-vxod5rknZGS3lAOtoi1EQgcIq_HDP4zLY0X-Rq",
        "https://www.logoground.com/uploads/201810872018-09-224869527GorillaGym.jpg",
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
        "planning" => json_encode([]),
        "class_id" => function () {
            return App\Classe::inRandomOrder()->first("id")->id;
        },
    ];
});
