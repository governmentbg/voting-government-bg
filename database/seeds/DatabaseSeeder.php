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
            AdminSeeder::class,
            VotingToursSeeder::class,
            OrganisationsSeeder::class,
            UserSeeder::class
        ]);
        $this->call(SystemUserSeeder::class);
    }
}
