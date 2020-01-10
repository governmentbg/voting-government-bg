<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class APIFileTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * Test get file data.
     *
     * @return void
     */
    public function testGetFileData()
    {
        $this->be(factory(\App\User::class)->create());
        $tourId = \App\VotingTour::getLatestTour() ? \App\VotingTour::getLatestTour()->id : factory(\App\VotingTour::class)->create()->id;

        $file = factory(\App\File::class)->create([
            'message_id'     => null,
            'org_id'         => factory(\App\Organisation::class)->create()->id,
            'voting_tour_id' => $tourId,
        ]);

        $response = $this->json('POST', '/api/file/getData', ['file_id' => $file->id]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
