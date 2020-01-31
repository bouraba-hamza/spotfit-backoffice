<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'qrcode' => $faker->md5,
        'firstName' => $faker->firstNameMale,
        'lastName' => $faker->lastName,
        'gender' => $faker-> randomElement($array = array ('m', 'f')),
        'birthDay' => $faker->date('Y-m-d', 'now'),
        'phoneNumber' => $faker->phoneNumber,
        'cin' => strtoupper($faker->randomLetter) . strtoupper($faker->randomLetter) .  $faker->randomNumber($nbDigits = 8, true),
        'jobTitle' => $faker->jobTitle,
        'avatar' => 'a' . $faker->randomElement([1, 2, 3, 4]) . '.png',
        'ambassador' => $faker-> randomElement($array = array (0, 1)),
    ];
});
