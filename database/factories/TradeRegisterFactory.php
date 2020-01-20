<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\TradeRegister;

$factory->define(TradeRegister::class, function (Faker $faker) {
    return [
        'eik' => $faker->unique()->ean8,
        'reg_number' => $faker->unique()->ean8,
        'reg_date' => $faker->date() .' '. $faker->time(),
        'name' => $faker->address,
        'representative' => $faker->text,
        'city' => $faker->city,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'status' => 'E',
        'status_date' => $faker->date() .' '. $faker->time(),
        'email' => $faker->unique()->safeEmail,
        'goals' => $faker->text,
        'tools' => $faker->text,
        'description' => $faker->text,
        'public_benefits' => $faker->boolean
    ];
});
