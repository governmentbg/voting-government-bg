<?php

use Faker\Factory as Faker;
use App\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    const USER_RECORDS = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();
        foreach (range(1, self::USER_RECORDS) as $i) {

            $username = $this->faker->unique()->name;
            User::create([
                'username'   => $this->faker->unique()->name,
                'password'   => bcrypt($username),
                'email'      => $this->faker->unique()->email,
                'first_name'  => $this->faker->firstName(),
                'last_name'   => $this->faker->lastName(),
                'active'     => 1,
                'created_by' => 1
            ]);
        }
    }
}
