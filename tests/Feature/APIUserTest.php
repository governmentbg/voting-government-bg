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

        $response = $this->json('POST', '/api/user/add', ['user_data' => $user->toArray()]);

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
            'password'       => Hash::make($password),
            'org_id'         => null,
            'voting_tour_id' => null,
        ]);

        $data = $user->toArray();
        $data['first_name'] = $this->faker->firstName;
        $data['last_name'] = $this->faker->lastName;
        $data['email'] = $this->faker->email;
        //unset($data['email']);

        $response = $this->json('POST', '/api/user/edit', [
            'user_id'   => $user->id,
            'user_data' => $data,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
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
            'password'       => Hash::make($password),
            'pw_reset_hash'  => $hash,
            'org_id'         => null,
            'voting_tour_id' => null,
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
        $user = factory(\App\User::class)->create([
            'username'       => $this->faker->username,
            'email'          => $this->faker->email,
            'org_id'         => null,
            'voting_tour_id' => null,
        ]);
        
        $response = $this->json('POST', '/api/user/generateHash', ['username' => $user->username, 'email' => $user->email]);

        $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                    ]);
    }

    /**
     * Test get user by id.
     *
     * @return void
     */
    public function testGetUserByID()
    {
        $user = factory(\App\User::class)->create(['org_id' => null, 'voting_tour_id' => null]);

        $response = $this->json('POST', '/api/user/getData', ['user_id' => $user->id]);

        $response->assertStatus(200)
                    ->assertJson([
                        'success' => true,
                    ]);
    }

    /**
     * Test get list of users.
     *
     * @return void
     */
    public function testlist()
    {
        $users = factory(\App\User::class, 10)->create(['org_id' => null, 'voting_tour_id' => null]);

        $response = $this->json('POST', '/api/user/list', ['order_field' => 'first_name', 'order_type' => 'DESC']);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
    
    /**
     * Test user change password fumctionality.
     *
     * @return void
     */
    public function testChangePassword()
    {
        $user = factory(\App\User::class)->create([
            'password'       => Hash::make('secret'),
            'org_id'         => null,
            'voting_tour_id' => null,
            ]);

        $response = $this->json('POST', '/api/user/changePassword', [
            'user_id'      => $user->id,
            'password'     => 'secret',
            'new_password' => 'notSecret',
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue(Hash::check('notSecret', $user->fresh()->password));
    }
}
