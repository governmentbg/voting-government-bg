<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(App\VotingTour::class, function (Faker $faker) {
    $userIds = User::select('id')->get();
    $userId = $userIds->isNotEmpty() ? $this->faker->randomElement($userIds)['id'] : factory(User::class)->create()->id;
    return [
        'name' => $faker->name,
        'status' => 3,
        'created_by' => $userId
    ];
});
