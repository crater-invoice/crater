<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\Country;
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
}
