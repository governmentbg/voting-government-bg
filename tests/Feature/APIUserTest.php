<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class APIUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    /**
     * Test API user creation.
     *
     * @return void
     */
    public function testAddUser()
    {
        $password = $this->faker->password(6, 10);
        $user = factory(\App\User::class)->make([
            'password'         => $password,
            'password_confirm' => $password,
        ]);
        
        $response = $this->json('POST', '/api/user/add', ['data' => $user->toArray()]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
    
    /**
     * Test API user update
     *
     * @return void
     */
    public function testEditUser()
    {
        $password = $this->faker->password(6, 10);
        $user = factory(\App\User::class)->create([
            'password' => Hash::make($password),
        ]);
        
        $data = $user->toArray();
        $data['first_name'] = $this->faker->firstName;
        $data['last_name'] = $this->faker->lastName;

        $response = $this->json('PUT', '/api/user/edit', [
            'id'   => $user->id,
            'data' => $data,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'id'      => $user->id,
            ]);
    }
    
    /**
     * Test reset password for user.
     *
     * @return void
     */
    public function testResetPassword()
    {
        $password = $this->faker->password(6, 10);
        $hash = Hash::make(str_random(60));
        
        $user = factory(\App\User::class)->create([
            'password'      => Hash::make($password),
            'pw_reset_hash' => $hash,
        ]);

        $response = $this->json('POST', '/api/user/passwordReset', ['new_password' => 'secret', 'hash' => $hash, 'id' => $user->id]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue(Hash::check($password, $user->password));
    }
    
    /**
     * Test generate password hash.
     *
     * @return void
     */
    public function testGeneratePasswordHash()
    {
        $response = $this->json('GET', '/api/user/generateHash');

        $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                    ]);
    }
}
