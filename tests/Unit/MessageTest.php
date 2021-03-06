<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;

class MessageTest extends TestCase
{
    use DatabaseTransactions;

    private $object;

    private $objectKey;

    private $faker;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();

        $faker = \Faker\Factory::create();
        $this->faker = $faker;

        $this->be(factory(\App\User::class)->create());
        //$this->objectKey = $faker->name;

        $this->object = factory(\App\Message::class)->create();
    }

    /**
     * Test Message creation in DB.
     *
     * @return void
     */
    public function testCreateMessage()
    {
        $this->assertDatabaseHas('messages', [
            'id' => $this->object->id,
        ]);
    }

    /**
     * Test Message update in DB.
     *
     * @return void
     */
    public function testUpdateMessage()
    {
        $newSubject = $this->faker->name;
        $this->object->update(['subject' => $newSubject]);

        $this->assertDatabaseHas('messages', [
            'subject' => $newSubject,
        ]);
    }

    /**
     * Test Message deletion in DB.
     *
     * @return void
     */
    public function testDeleteMesage()
    {
        $this->object->delete();

        $this->assertDatabaseMissing('messages', [
            'id' => $this->object->id,
        ]);
    }

    public function tearDown(): void
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
