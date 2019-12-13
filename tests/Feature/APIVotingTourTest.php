<?php
namespace Tests\Unit\Api;

use App\VotingTour;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APIVotingTourTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private $votingTour;

    public function setUp()
    {
        parent::setUp();
        $this->be(factory(\App\User::class)->create());

        $this->votingTour = VotingTour::where('status', '!=', VotingTour::STATUS_FINISHED)->first();

        if (!$this->votingTour) {
            $this->votingTour = VotingTour::orderBy('created_at', 'DESC')->first();
        }

        if (!$this->votingTour) {
            $this->votingTour = VotingTour::create([
                'name'   => $this->faker->name(),
                'status' => VotingTour::STATUS_UPCOMING,
            ]);
        }
    }

    /**
     * Test voting tour creation
     *
     * @return void
     */
    public function testAddVotingTour()
    {
        $votingTour = VotingTour::where('status', '!=', VotingTour::STATUS_FINISHED)->first();

        if (!$votingTour) {
            $this->post(url('api/votingTour/add'), ['name' => $this->faker->name()])
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        } else {
            $this->post(url('api/votingTour/add'), ['name' => $this->faker->name()])
                ->assertStatus(500)
                ->assertJson(['success' => false]);
        }
    }

    /**
     * Test voting tour status change
     *
     * @return void
     */
    public function testChangeTourStatus()
    {
        if ($this->votingTour->status == VotingTour::STATUS_FINISHED) {
            $newStatus = VotingTour::STATUS_FINISHED;
        } else {
            $newStatus = $this->votingTour->status + VotingTour::STATUS_STEP;
        }

        $this->post(url('api/votingTour/changeStatus'), ['new_status' => $newStatus])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test voting tour rename
     *
     * @return void
     */
    public function testRenameTour()
    {
        $this->post(url('api/votingTour/rename'), ['new_name' => $this->faker->name()])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test get latest voting tour
     *
     * @return void
     */
    public function testGetLatestVotingTour()
    {
        $this->post(url('api/votingTour/getLatestVotingTour'), [])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test voting tour list
     *
     * @return void
     */
    public function testListVotingTours()
    {
        $this->post(url('api/votingTour/list'), ['order_by' => 'status', 'order_type' => 'ASC'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Test voting tour data
     *
     * @return void
     */
    public function testGetTourData()
    {
        $this->post(url('api/votingTour/getData'), ['tour_id' => $this->votingTour->id])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
