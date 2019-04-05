<?php

use Faker\Generator as Faker;

$factory->define(App\VotingTour::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'status' => 6,
        'created_by' => 1
    ];
});
