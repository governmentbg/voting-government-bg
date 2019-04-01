<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class VoteTest extends TestCase
{
    use RefreshDatabase;
    
    private $object;
    
    private $objectKey;
    
    private $faker;

    public function setUp()
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        
        $faker = \Faker\Factory::create();   
        $this->faker = $faker;
        
        //$this->objectKey = $faker->name;
        
        $this->object = factory(\App\Vote::class)->create();
    }
    

    /**
     * Test Vote creation in DB.
     *
     * @return void
     */
    public function testCreateVote()
    {      
        $this->assertDatabaseHas('votes', [
            'id' => $this->object->id
        ]);
    }
    
    /**
     * Test Vote update in DB.
     *
     * @return void
     */
    public function testUpdateVote()
    {      
        $newStatus = $this->faker->numberBetween(1, 55);
        $this->object->update(['tour_status' => $newStatus]);
        
        $this->assertDatabaseHas('votes', [
            'tour_status' => $newStatus
        ]);      
    }
    
    /**
     * Test Vote deletion in DB.
     *
     * @return void
     */
    public function testDeleteVote()
    {      
        $this->object->delete();
        
        $this->assertDatabaseMissing('votes', [
            'id' => $this->object->id
        ]);  
    }
    
    public function tearDown()
    {
        Schema::enableForeignKeyConstraints();       
        parent::tearDown();      
    }
}
