<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APIFileTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test get file data.
     *
     * @return void
     */
    public function testGetFileData()
    {
        $file = factory(\App\File::class)->create([
            'message_id' => null,
            'org_id'     => \App\Organisation::first(),
        ]);

        $response = $this->json('POST', '/api/file/getData', ['file_id' => $file->id]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
