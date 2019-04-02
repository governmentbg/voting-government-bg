<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class FileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    
    private $object;
    
    private $objectKey;

    public function setUp()
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
     * Test File update in DB.
     *
     * @return void
     */
    public function testUpdateFile()
    {
        $newType = $this->faker->mimeType;
        $this->object->update(['mime_type' => $newType]);
        
        $this->assertDatabaseHas('files', [
            'mime_type' => $newType,
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
    
    public function tearDown()
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
