<?php

use Faker\Generator as Faker;

$factory->define(App\Organisation::class, function (Faker $faker) {
    return [
        'eik' => $faker->ean8,
        'voting_tour_id' => $faker->numberBetween(1, 10),
        'name' => $faker->name,
        //'address' => $faker->address,
        'representative' => $faker->name,
        'email' => $faker->email,
        'in_ap' => 1,
        'is_candidate' => 1,
        'description' => $faker->text,
        'reference' => $faker->text,
        'status' => $faker->numberBetween(0, 10),
        'status_hint' => $faker->numberBetween(0, 10)
    ];
});
