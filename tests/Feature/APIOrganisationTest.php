<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APIOrganisationTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->be(factory(\App\User::class)->create());
        $tour = \App\VotingTour::getLatestTour() ? \App\VotingTour::getLatestTour() : factory(\App\VotingTour::class)->create();
    }

    /**
     * Test API organisation creation.
     *
     * @return void
     */
    public function testRegisterOrg()
    {
        $org = factory(\App\Organisation::class)->create();
        $response = $this->json('POST', '/api/organisation/register', ['org_data' => $org->toArray()]);

        $votingTour = \App\VotingTour::getLatestTour();
        if (!empty($votingTour) && $votingTour->status == \App\VotingTour::STATUS_OPENED_REG) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test API organisation update
     *
     * @return void
     */
    public function testEditOrg()
    {
        $org = factory(\App\Organisation::class)->create();

        $data = $org->toArray();
        $data['name'] = $this->faker->name;
        $data['email'] = $this->faker->email;
        $data['status_hint'] = null;

        $response = $this->json('POST', '/api/organisation/edit', [
            'org_id'   => $org->id,
            'org_data' => $data,
        ]);

        $votingTour = \App\VotingTour::getLatestTour();
        if (!empty($votingTour) && in_array($votingTour->status, \App\VotingTour::getRegStatuses())) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test get organisation data.
     *
     * @return void
     */
    public function testGetOrgData()
    {
        $org = factory(\App\Organisation::class)->create();
        $response = $this->json('POST', '/api/organisation/getData', ['org_id' => $org->id]);

        $votingTour = \App\VotingTour::getLatestTour();
        if (!empty($votingTour) && $org->voting_tour_id == $votingTour->id) {
            $response->assertStatus(200)->assertJson(['success' => true]);
        } else {
            $response->assertStatus(500)->assertJson(['success' => false]);
        }
    }

    /**
     * Test organisations listing by filters.
     *
     * @return void
     */
    public function testSearchOrgs()
    {
        $orgs = factory(\App\Organisation::class, 10)->create();

        $response = $this->json('POST', '/api/organisation/search', [
            'filters'     => ['reg_date_from' => '2019-04-01'],
            'order_field' => 'created_at',
            'order_type'  => 'DESC',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test list organisation files.
     *
     * @return void
     */
    public function testGetOrgFileList()
    {
        $org = factory(\App\Organisation::class)->create();

        $response = $this->json('POST', '/api/organisation/getFileList', ['org_id' => $org->id]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test list statuses.
     *
     * @return void
     */
    public function testListStatuses()
    {
        $response = $this->json('POST', '/api/organisation/listStatuses');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test list candidate statuses.
     *
     * @return void
     */
    public function testListCandidateStatuses()
    {
        $response = $this->json('POST', '/api/organisation/listCandidateStatuses');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
