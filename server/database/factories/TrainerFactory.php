<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Trainer;
use Faker\Generator as Faker;

$factory->define(Trainer::class, function (Faker $faker) {
    return [
        'weight' => $faker->numberBetween(25, 300),
        'length' => $faker->randomFloat(1, 1, 4),
        'firstName' => $faker->firstNameMale,
        'lastName' => $faker->lastName,
        'gender' => $faker-> randomElement($array = array ('m', 'f')),
        'birthDay' => $faker->date('Y-m-d', 'now'),
        'phoneNumber' => $faker->phoneNumber,
        'cin' => strtoupper($faker->randomLetter) . strtoupper($faker->randomLetter) .  $faker->randomNumber($nbDigits = 8, true),
        'jobTitle' => $faker->jobTitle,
        'avatar' => 'a' . $faker->randomElement([1, 2, 3, 4]) . '.png',
    ];
});
