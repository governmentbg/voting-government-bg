<?php

use Faker\Generator as Faker;

$factory->define(App\Organisation::class, function (Faker $faker) {
    return [
        'eik' => $faker->ean8,
        'voting_tour_id' => $faker->numberBetween(1, 10),
        'name' => $faker->name,
        'address' => $faker->address,
        'representative' => $faker->name,
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'in_av' => 1,
        'is_candidate' => 1,
        'description' => $faker->text,
        'references' => $faker->text,
        'status' => $faker->numberBetween(0, 5),
        'status_hint' => $faker->numberBetween(0, 10)
    ];
});
