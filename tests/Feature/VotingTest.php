<?php
namespace Tests\Unit\Api;

use App\VotingTour;
use App\Vote;
use Tests\TestCase;
use App\Organisation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VoteTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private $votingTourId;
    private $organisation;
    private $vote;

    public function setUp()
    {
        parent::setUp();

        $this->votingTourId = factory(VotingTour::class)->create()->id;
        $this->organisation = factory(Organisation::class)->create()->id;

        $t = microtime(true);
        $micro = sprintf('%06d',($t - floor($t)) * 1000000);
        $d = new \DateTime(date('Y-m-d H:i:s.'. $micro, $t));

        $this->vote = Vote::create([
            'vote_time' => $this->faker->dateTime(time()),
            'voter_id' => $this->organisation,
            'voting_tour_id' => $this->votingTourId,
            'vote_data' => $this->organisation,
            'tour_status' => VotingTour::STATUS_VOTING
        ]);
    }

    /**
     * Test listing of voters
     *
     * @return void
     */
    public function testListVoters()
    {
        $this->post(url('api/vote/listVoters'), ['tour_id' => $this->votingTourId])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test getting vote status
     *
     * @return void
     */
    public function testGetVoteStatus()
    {
        $this->post(url('api/vote/getVoteStatus'), ['tour_id' => $this->votingTourId])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
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
        $this->post(url('api/vote/getLatestVote'), ['org_id' => $this->organisation])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test voting
     *
     * @return void
     */
    public function testVote()
    {
        $string = strval($this->organisation);

        $this->post(url('api/vote/vote'), ['org_id' => $this->organisation, 'org_list' => $string])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test ranking
     *
     * @return void
     */
    public function testRanking()
    {
        $this->post(url('api/vote/ranking'), ['tour_id' => $this->votingTourId, 'status' => VotingTour::STATUS_VOTING])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
