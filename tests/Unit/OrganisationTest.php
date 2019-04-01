<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;
    
    private $org;
    
    private $orgId;
    
    private $faker;

    public function setUp()
    {
        parent::setUp();
        Schema::disableForeignKeyConstraints();
        
        $faker = \Faker\Factory::create();   
        $this->faker = $faker;
        
        $this->orgId = $faker->ean8;
        
        $this->org = factory(\App\Organisation::class)->create([
            'eik' => $this->orgId,
        ]);
    }
    
    public function tearDown()
    {
        Schema::enableForeignKeyConstraints();       
        parent::tearDown();      
    }
    
    
    /**
     * Test Organisation creation in DB.
     *
     * @return void
     */
    public function testCreateOrganisation()
    {       
        $this->assertDatabaseHas('organisations', [
            'eik' => $this->orgId
        ]);
    }
    
    /**
     * Test Organisation update in DB.
     *
     * @return void
     */
    public function testUpdateOrganisation()
    {      
        $newOrgId = $this->faker->ean8;
        
        $this->org->update(['eik' => $newOrgId]);
        
        $this->assertDatabaseHas('organisations', [
            'eik' => $newOrgId
        ]);      
    }
    
    /**
     * Test Organisation deletion in DB.
     *
     * @return void
     */
    public function testDeleteOrganisation()
    {      
        $this->org->delete();
        
        $this->assertDatabaseMissing('organisations', [
            'eik' => $this->orgId
        ]);  
    }
}
