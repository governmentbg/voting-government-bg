<?php
namespace Tests\Unit\Api;

use App\VotingTour;
use Tests\TestCase;
use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VotingTourTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    private $votingTour;

    public function setUp()
    {
        parent::setUp();
        $this->votingTour = VotingTour::where('status', '!=', VotingTour::STATUS_FINISHED)->first();

        if (!$this->votingTour) {
            $this->votingTour = VotingTour::orderBy('created_at', 'DESC')->first();
        }

        if (!$this->votingTour) {
            $this->votingTour = VotingTour::create([
                'name'              => $this->faker->name(),
                'status'            => $this->faker->name()
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
            $this->post(url('api/votingTours/add'), ['name' => $this->faker->name()])
                ->assertStatus(200)
                ->assertJson(['success' => true]);
        } else {
            $this->post(url('api/votingTours/add'), ['name' => $this->faker->name()])
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
        $this->post(url('api/votingTours/changeStatus'), ['new_status' => rand(0, 6)])
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
        $this->post(url('api/votingTours/rename'), ['new_name' => $this->faker->name()])
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
        $this->post(url('api/votingTours/getLatestVotingTour'), [])
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
        $this->post(url('api/votingTours/list'), ['order_by' => 'status', 'order_type' => 'ASC'])
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
        $votingTour = VotingTour::select('id')->first();

        $this->post(url('api/votingTours/getData'), ['id' => $this->votingTour->id])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}