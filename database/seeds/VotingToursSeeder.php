<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class VotingToursSeeder extends Seeder
{
    const TOUR_RECORDS = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\VotingTour::class, self::TOUR_RECORDS)->create()->each(function ($u) {
            $u->save();
        });
    }
}
