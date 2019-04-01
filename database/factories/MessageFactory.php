<?php

use Faker\Generator as Faker;

$factory->define(App\Message::class, function (Faker $faker) {
    return [
        'sender_org_id' => $faker->numberBetween(1, 100),
        'sender_user_id' => null,//$faker->numberBetween(1, 100),
        'subject' => $faker->text,
        'body' => $faker->text,
        'read' => null,
        'parent_id' => null,
        'recipient_org_id' => null, //$faker->numberBetween(1, 100),
    ];
});
