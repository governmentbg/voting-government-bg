<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class SystemUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::where('username', config('auth.system.user'))->first()){
            factory(\App\User::class)->create([
                'username' => config('auth.system.user'),
                'password' => Hash::make(str_random(60)),
                'active' => 1,
                'first_name' => config('auth.system.user'),
                'last_name' => config('auth.system.user'),
                'email' => config('auth.system.user'),
            ]);
        }
        else{
            if (isset($this->command)) {
                $this->command->warn("System user already exists!");
            }
        }
    }
}
