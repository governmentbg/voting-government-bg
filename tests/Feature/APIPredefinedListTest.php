<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\BulstatRegister;
use App\TradeRegister;
use App\PredefinedOrganisation;

class APIPredefinedListTest extends TestCase
{
    use WithFaker;

    /**
     * Test get bulstat register data.
     *
     * @return void
     */
    public function testGetBulstatRegisterData()
    {
        $response = $this->json('POST', '/api/predefinedList/getData', [
            'type' => BulstatRegister::PREDEFINED_LIST_TYPE,
            'eik'  => $this->faker->unique()->ean8
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test get trade register data.
     *
     * @return void
     */
    public function testGetTradeRegisterData()
    {
        $response = $this->json('POST', '/api/predefinedList/getData', [
            'type' => TradeRegister::PREDEFINED_LIST_TYPE,
            'eik'  => $this->faker->unique()->ean8
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test get predefined org data.
     *
     * @return void
     */
    public function testGetPredefinedOrgData()
    {
        $response = $this->json('POST', '/api/predefinedList/getData', [
            'type' => PredefinedOrganisation::PREDEFINED_LIST_TYPE,
            'eik'  => $this->faker->unique()->ean8
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test update bulstat register data.
     *
     * @return void
     */
    public function testUpdateBulstatRegister()
    {
        $data = factory(\App\BulstatRegister::class)->make()->toArray();

        $response = $this->json('POST', '/api/predefinedList/update', [
            'type' => BulstatRegister::PREDEFINED_LIST_TYPE,
            'data' => $data
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test update trade register data.
     *
     * @return void
     */
    public function testUpdateTradeRegister()
    {
        $data = factory(\App\TradeRegister::class)->make()->toArray();

        $response = $this->json('POST', '/api/predefinedList/update', [
            'type' => TradeRegister::PREDEFINED_LIST_TYPE,
            'data' => $data
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    /**
     * Test update predefined organisation data.
     *
     * @return void
     */
    public function testUpdatePredefinedOrg()
    {
        $data = factory(\App\PredefinedOrganisation::class)->make()->toArray();

        $response = $this->json('POST', '/api/predefinedList/update', [
            'type' => PredefinedOrganisation::PREDEFINED_LIST_TYPE,
            'data' => $data
        ]);

        $response->assertStatus(500)->assertJson(['success' => false]);
    }

    /**
     * Test list types.
     *
     * @return void
     */
    public function testListTypes()
    {
        $response = $this->json('POST', '/api/predefinedList/listTypes');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
