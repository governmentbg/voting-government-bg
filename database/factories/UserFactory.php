<?php

use App\User;
use App\VotingTour;
use App\Organisation;
use Illuminate\Support\Str;
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
    $votingTours = VotingTour::select('id')->get();
    $orgIds = Organisation::doesntHave('user')->select('id')->get();
    $orgId = $orgIds->isNotEmpty() ? $this->faker->unique()->randomElement($orgIds)['id'] : factory(Organisation::class)->create()->id;

    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'active' => 1,
        'username' => $faker->name,
        'voting_tour_id' => $this->faker->randomElement($votingTours)['id'],
        'org_id' => $orgId,
    ];
});
