<?php

use Faker\Generator as Faker;
use App\VotingTour;
use App\User;

$factory->define(App\Organisation::class, function (Faker $faker) {
    $tourIds = VotingTour::select('id')->get();
    $tourId = $tourIds->isNotEmpty() ? $this->faker->randomElement($tourIds)['id'] : factory(VotingTour::class)->create()->id;
    
    $userIds = User::select('id')->get();
    $userId = $userIds->isNotEmpty() ? $this->faker->randomElement($userIds)['id'] : factory(User::class)->create()->id;

    return [
        'eik' => $faker->ean8,
        'voting_tour_id' =>  $tourId,
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
        'status_hint' => $faker->numberBetween(0, 10),
        'created_by' => $userId
    ];
});
