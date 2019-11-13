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
        $votingTour = VotingTour::where('status', '>=', VotingTour::STATUS_OPENED_REG)->first();

        if (!$votingTour) {
            $this->post(url('api/actionHistory/search'))
                ->assertStatus(500)
                ->assertJson(['success' => false]);
        } else {
            $data['filters']['voting_tour_id'] = $votingTour->id;

            $this->post(url('api/actionHistory/search'), $data)
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        }
    }
}
