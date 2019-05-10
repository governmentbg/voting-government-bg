<?php

use Faker\Generator as Faker;

$factory->define(App\Vote::class, function (Faker $faker) {
    return [
        // 'vote_time' => $faker->dateTime,
        // 'voter_id' => $faker->numberBetween(1, 100),
        // 'vote_data' => $faker->text,
        // 'tour_status' => $faker->numberBetween(1, 100),
        // 'prev_hash' => md5($faker->text(100)),
    ];
});
