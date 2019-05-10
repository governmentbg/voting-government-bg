<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class OrganisationsSeeder extends Seeder
{
    const ORGANISATION_RECORDS = 13000;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Organisation::class, self::ORGANISATION_RECORDS)->create()->each(function ($u) {
            $u->save();
        });
    }
}
