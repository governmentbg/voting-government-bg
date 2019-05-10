<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SystemUserSeeder::class,
            VotingToursSeeder::class,
            OrganisationsSeeder::class,
            UserSeeder::class,
            VotesSeeder::class
        ]);
    }
}
