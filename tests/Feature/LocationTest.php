<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\Country;
use Crater\State;
use Crater\City;
use SettingsSeeder;
class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(SettingsSeeder::class);
    }

    /** @test */
    public function testGetCountries()
    {
        $response = $this->json('GET', 'api/countries');

        $response->assertOk();
    }

    /** @test */
    public function testGetStates()
    {
        $response = $this->json('GET', 'api/states/1');

        $response->assertOk();
    }

    /** @test */
    public function testGetCities()
    {
        $response = $this->json('GET', 'api/cities/1');

        $response->assertOk();
    }
}
