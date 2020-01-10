<?php

namespace Tests\Unit;

use App\VotingTour;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;

class VotingTourTest extends TestCase
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
            'id' => $this->object->id,
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
            'status' => $newStatus,
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
            'id' => $this->object->id,
        ]);
    }

    public function tearDown(): void
    {
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }
}
