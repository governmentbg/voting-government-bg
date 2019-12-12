<?php
namespace Tests\Unit\Api;

use App\VotingTour;
use App\Vote;
use Tests\TestCase;
use App\Organisation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APIVoteTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private $votingTour;
    private $orgId;
    private $vote;

    public function setUp()
    {
        parent::setUp();

        $votingTour = VotingTour::getLatestTour();
        if (!empty($votingTour)) {
            $this->votingTour = $votingTour;
        } else {
            $this->votingTour = factory(VotingTour::class)->create();
        }

        $this->orgId = null;
        if (!empty($this->votingTour)) {
            $orgData = Organisation::where('voting_tour_id', $this->votingTour->id)
                        ->where('is_candidate', 1)
                        ->whereIn('status', Organisation::getApprovedCandidateStatuses())
                        ->first();
            if (!empty($orgData)) {
                $this->orgId = $orgData->id;
            } else {
                $this->orgId = factory(Organisation::class)->create()->id;
            }

            $tourStatus = null;
            if ($this->votingTour->status == VotingTour::STATUS_VOTING) {
                $tourStatus = Vote::TOUR_VOTING;
            } elseif($this->votingTour->status == VotingTour::STATUS_BALLOTAGE) {
                $tourStatus = Vote::TOUR_BALLOTAGE;
            }
            if (!empty($this->orgId) && !is_null($tourStatus)) {
                $t = microtime(true);
                $micro = sprintf('%06d',($t - floor($t)) * 1000000);
                $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

                $this->vote = Vote::create([
                    'vote_time' => $this->faker->dateTime(time()),
                    'voter_id' => $this->orgId,
                    'voting_tour_id' => $this->votingTour->id,
                    'vote_data' => $this->orgId,
                    'tour_status' => $tourStatus
                ]);
            }
        }
    }

    /**
     * Test listing of voters
     *
     * @return void
     */
    public function testListVoters()
    {
        $response = $this->post(url('api/vote/listVoters'), []);

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test blockchain validation
     *
     * @return void
     */
    public function testIsBlockChainValid()
    {
        $this->post(url('api/vote/isBlockChainValid'), [])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test getting latest vote for a given organisation
     *
     * @return void
     */
    public function testGetLatestVote()
    {
        $response = $this->post(url('api/vote/getLatestVote'), ['org_id' => $this->orgId]);

        if (!empty($this->votingTour) && array_key_exists($this->votingTour->status, VotingTour::getActiveStatuses())) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test voting
     *
     * @return void
     */
    public function testVote()
    {
        $response = $this->post(url('api/vote/vote'), ['org_id' => $this->orgId, 'org_list' => strval($this->orgId)]);

        if (!empty($this->orgId) && !empty($this->votingTour) && array_key_exists($this->votingTour->status, VotingTour::getActiveStatuses())) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test ranking
     *
     * @return void
     */
    public function testRanking()
    {
        if (!empty($this->votingTour) && Vote::getVotingCount($this->votingTour->id) > 0) {
            $rankingStatus = Vote::TOUR_BALLOTAGE_RANKING;
        } elseif($this->votingTour->status == VotingTour::STATUS_BALLOTAGE) {
            $rankingStatus = Vote::TOUR_RANKING;
        }

        $response = $this->post(url('api/vote/ranking'), ['status' => $rankingStatus]);

        if (!empty($this->votingTour) && $this->votingTour->status == VotingTour::STATUS_RANKING) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test getting latest ranking
     *
     * @return void
     */
    public function testGetLatestRanking()
    {
        $response =  $this->post(url('api/vote/getLatestRanking'), ['tour_id' => $this->votingTour->id]);

        if (!empty($this->votingTour) && !in_array($this->votingTour->status, VotingTour::getRegStatuses())) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test tour cancelling
     *
     * @return void
     */
    public function testCancelTour()
    {
        $response =  $this->post(url('api/vote/cancelTour'), []);

        if (!empty($this->votingTour) && $this->votingTour->status == VotingTour::STATUS_FINISHED) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }
}
