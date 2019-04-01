<?php

use Faker\Generator as Faker;

$factory->define(App\VotingTour::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => $faker->numberBetween(1, 100),       
    ];
});
