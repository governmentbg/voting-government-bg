<?php
namespace Tests\Unit\Api;

use App\VotingTour;
use Tests\TestCase;
use App\ActionsHistory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APIActionsHistoryTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private $votingTour;

    /**
     * Test voting tour creation
     *
     * @return void
     */
    public function testListActionsHistory()
    {
        $votingTour = \App\VotingTour::getLatestTour() ? \App\VotingTour::getLatestTour() : null;

        // logging before adding a tour
        if ($votingTour == null) {
            $this->post(url('api/actionHistory/search'))
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        } else {
             // logging for a specific tour
            $data['filters']['voting_tour_id'] = $votingTour->id;

            $this->post(url('api/actionHistory/search'), $data)
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        }
    }
}
