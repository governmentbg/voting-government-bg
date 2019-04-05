<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    const USER_RECORDS = 100;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, self::USER_RECORDS)->create()->each(function ($u) {
            $u->save();
        });
    }
}
