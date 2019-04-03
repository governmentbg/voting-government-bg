<?php

use Faker\Generator as Faker;
use App\VotingTour;

$factory->define(App\Organisation::class, function (Faker $faker) {
    $tourIds = VotingTour::select('id')->get();

    return [
        'eik' => $faker->ean8,
        'voting_tour_id' =>  $this->faker->unique()->randomElement($tourIds)['id'],
        'name' => $faker->name,
        //'address' => $faker->address,
        'representative' => $faker->name,
        'email' => $faker->email,
        'in_ap' => 1,
        'is_candidate' => 1,
        'description' => $faker->text,
        'reference' => $faker->text,
        'status' => $faker->numberBetween(0, 10),
        'status_hint' => $faker->numberBetween(0, 10),
        'created_by' => 1
    ];
});
