<?php

namespace Tests\Unit;

use App\VotingTour;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class VotingTourTest extends TestCase
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

        $activeVotingTour = VotingTour::where('status', '!=', VotingTour::STATUS_FINISHED)->first();

        if (!$activeVotingTour) {
            $activeVotingTour = VotingTour::orderBy('created_at', 'DESC')->first();
        }

        $this->object = $activeVotingTour ? $activeVotingTour : factory(\App\VotingTour::class)->create();
    }

    /**
     * Test Voting tour creation in DB.
     *
     * @return void
     */
    public function testCreateVotingTour()
    {
        $this->assertDatabaseHas('voting_tour', [
            'id' => $this->object->id
        ]);
    }

    /**
     * Test Voting tour update in DB.
     *
     * @return void
     */
    public function testUpdateVotingTour()
    {
        $newStatus = $this->faker->numberBetween(1, 55);
        $this->object->update(['status' => $newStatus]);

        $this->assertDatabaseHas('voting_tour', [
            'status' => $newStatus
        ]);
    }

    /**
     * Test Voting tour deletion in DB.
     *
     * @return void
     */
    public function testDeleteVotingTour()
    {
        $this->object->delete();

        $this->assertDatabaseMissing('voting_tour', [
            'id' => $this->object->id
        ]);
    }

    public function tearDown()
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
