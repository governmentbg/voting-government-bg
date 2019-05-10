<?php

use App\User;
use App\Organisation;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $tourId = null;
    $orgId = null;
    $organisations = Organisation::doesntHave('user')->select('id', 'voting_tour_id')->get();
    if ($organisations->isNotEmpty()) {
        if ($orgData = $this->faker->unique()->randomElement($organisations)) {
            $tourId = $orgData['voting_tour_id'];
            $orgId = $orgData['id'];
        }
    }

    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'active' => 1,
        'username' => $faker->unique()->name,
        'voting_tour_id' => $tourId,
        'org_id' => $orgId,
    ];
});
