<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;

class FileTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    private $object;

    private $objectKey;

    public function setUp(): void
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();

        $this->be(factory(\App\User::class)->create());

        //$this->objectKey = $faker->name;

        $this->object = factory(\App\File::class)->create();
    }

    /**
     * Test File creation in DB.
     *
     * @return void
     */
    public function testCreateFile()
    {
        $this->assertDatabaseHas('files', [
            'id' => $this->object->id,
        ]);
    }

    /**
     * Test File deletion in DB.
     *
     * @return void
     */
    public function testDeleteFile()
    {
        $this->object->delete();

        $this->assertDatabaseMissing('files', [
            'id' => $this->object->id,
        ]);
    }

    public function tearDown(): void
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
