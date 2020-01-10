<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    private $username;

    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();

        $faker = \Faker\Factory::create();
        $this->faker = $faker;

        $this->username = $faker->name;

        $this->user = factory(\App\User::class)->create([
            'username' => $this->username,
        ]);
    }

    /**
     * Test User creation in DB.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $this->assertDatabaseHas('users', [
            'username' => $this->username,
        ]);
    }

    /**
     * Test User update in DB.
     *
     * @return void
     */
    public function testUpdateUser()
    {
        $newUsername = $this->faker->name;
        $this->user->update(['username' => $newUsername]);

        $this->assertDatabaseHas('users', [
            'username' => $newUsername,
        ]);
    }

    /**
     * Test User deletion in DB.
     *
     * @return void
     */
    public function testDeleteUser()
    {
        $this->user->delete();

        $this->assertDatabaseMissing('users', [
            'username' => $this->username,
        ]);
    }

    public function tearDown(): void
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
