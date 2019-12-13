<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class APIMessageTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    private $org;

    public function setUp()
    {
        parent::setUp();
        //Schema::disableForeignKeyConstraints();

        $this->user = factory(\App\User::class)->create();
        $this->tour = factory(\App\VotingTour::class)->create(['created_by' => $this->user->id]);
        $this->org = factory(\App\Organisation::class)->create([
            'created_by'     => $this->user->id,
            'voting_tour_id' => $this->tour->id,
            ]);

        $this->be($this->user);
    }

    public function tearDown()
    {
        //Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }

    /**
     * Test mark message as read.
     *
     * @return void
     */
    public function testMarkMessageAsRead()
    {
        $message = factory(\App\Message::class)->create([
            'sender_org_id'  => $this->org->id,
            'voting_tour_id' => $this->tour->id,
            ]);

        $response = $this->json('POST', '/api/message/markAsRead', ['message_id' => $message->id]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue($message->fresh()->read != null);
    }

    /**
     * Test search/filter messages.
     *
     * @return void
     */
    public function testSearch()
    {
        factory(\App\Message::class, 2)->create([
            'read'           => null,
            'sender_org_id'  => $this->org->id,
            'voting_tour_id' => $this->tour->id,
            ]);
        factory(\App\Message::class, 5)->create([
                    'read'           => date('Y-m-d H:i:s'),
                    'sender_org_id'  => $this->org->id,
                    'sender_org_id'  => $this->org->id,
                    'voting_tour_id' => $this->tour->id,
                ]);
        factory(\App\Message::class)->create([
                    'sender_org_id' => factory(\App\Organisation::class)->create(
                            ['name' => 'new org', 'voting_tour_id' => $this->tour->id])->id,
                    'voting_tour_id' => $this->tour->id,
                ]);

        //get not read messages
        $response = $this->json('POST', '/api/message/search', ['filters' => ['status' => 1]]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertAttributeEquals(3, 'total', $response->getData()->data); //not read count
        //get only read messages
        $response = $this->json('POST', '/api/message/search', ['filters' => ['status' => 2]]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertAttributeEquals(5, 'total', $response->getData()->data); //not read count
        //find org by name
        $response = $this->json('POST', '/api/message/search', ['filters' => ['org_name' => 'new org']]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertAttributeEquals(1, 'total', $response->getData()->data); //not read count
    }

    /**
     * Test listing messages by organisation sender id.
     *
     * @return void
     */
    public function testListBySenderOrg()
    {
        $messages = factory(\App\Message::class, 10)->create([
            'sender_user_id'   => null,
            'sender_org_id'    => $this->org->id,
            'recipient_org_id' => null,
            'voting_tour_id'   => $this->tour->id,
        ]);

        $response = $this->json('POST', '/api/message/listByOrg', ['org_id' => $this->org->id]);
        $data = $response->getData();

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertAttributeEquals(10, 'total', $data->data); //test piginator object property
    }

    /**
     * Test listing messages by parent message id.
     *
     * @return void
     */
    public function testListByParentId()
    {
        $parentMessage = factory(\App\Message::class)->create([
            'sender_org_id'  => $this->org->id,
            'voting_tour_id' => $this->tour->id,
        ]);
        $message = factory(\App\Message::class, 10)->create([
            'sender_user_id'   => $this->user->id,
            'sender_org_id'    => null,
            'recipient_org_id' => $this->org->id,
            'parent_id'        => $parentMessage->id,
            'voting_tour_id'   => $this->tour->id,
        ]);

        $response = $this->json('POST', '/api/message/listByParent', ['parent_id' => $parentMessage->id]);
        $data = $response->getData();

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test listing message statuses.
     *
     * @return void
     */
    public function testListStatuses()
    {
        $response = $this->json('POST', '/api/message/listStatuses');

        $response->assertStatus(200)->assertJson(['success' => true])->assertJsonCount(3, 'data');
    }

    /**
     * Test sending message to org.
     *
     * @return void
     */
    public function testSendMessageToOrg()
    {
        $message = factory(\App\Message::class)->make([
            'sender_user_id'   => $this->user->id,
            'sender_org_id'    => null,
            'recipient_org_id' => $this->org->id,
        ]);

        $response = $this->json('POST', '/api/message/sendMessageToOrg', $message->toArray());

        $response->assertStatus(200)->assertJson(['success' => true]);

        $id = $response->getData()->id;
        $this->assertDatabaseHas('messages', [
            'id' => $id,
        ]);
    }

    /**
     * Test sending message from org.
     *
     * @return void
     */
    public function testSendMessageFromOrg()
    {
        $message = factory(\App\Message::class)->make(['sender_org_id' => $this->org->id]);

        $data = array_merge($message->toArray(),
                [
                    'files' => [
                        [
                            'name'      => 'test file',
                            'mime_type' => 'image/png',
                            'data'      => base64_encode($this->faker->text(200)),
                        ],
                    ],
                ]);

        $response = $this->json('POST', '/api/message/sendMessageFromOrg', $data);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $id = $response->getData()->id;
        $this->assertDatabaseHas('messages', [
            'id' => $id,
        ]);

        $this->assertDatabaseHas('files', [
            'name' => 'test file',
        ]);
    }
}
