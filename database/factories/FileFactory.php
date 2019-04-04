<?php

use Faker\Generator as Faker;

$factory->define(App\File::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'data' => $faker->text,
        'mime_type' => $faker->mimeType,
        'message_id' => $faker->numberBetween(1, 100),
        'org_id' => $faker->numberBetween(1, 100),
        'voting_tour_id' => $faker->numberBetween(1, 10),
    ];
});
