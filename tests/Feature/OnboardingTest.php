<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\Address;

class OnboardingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function testGetOnboardingData()
    {
        $this->seed();
        $response = $this->json('GET', 'api/admin/onboarding');

        $response->assertOk();

        $status = $response->decodeResponseJson()['profile_complete'];
        $this->assertEquals($status, 0);
    }

    /** @test */
    public function testUpdateOnboardingProfile()
    {
        $user = [
            'name' => 'Jane Doe',
            'password' => 'admin@123',
            'email' => 'admin@crater.in'
        ];

        $response = $this->json('POST', 'api/admin/onboarding/profile', $user);

        $user2 = $response->decodeResponseJson()['user'];
        $response->assertOk();
        $this->assertEquals($user['name'], $user2['name']);
        $this->assertEquals($user['email'], $user2['email']);
    }

    /** @test */
    public function testGetOnboardingDataAfterProfileUpdate()
    {
        $response = $this->json('GET', 'api/admin/onboarding');

        $response->assertOk();

        $status = $response->decodeResponseJson()['profile_complete'];
        $this->assertEquals($status, '1');
    }

    /** @test */
    public function testUpdateCompanyDetails()
    {
        $company = [
            'name' => 'XYZ',
            'country_id' => 2
        ];

        $response = $this->json('POST', 'api/admin/onboarding/company', $company);

        $response->assertOk();
        $company2 = $response->decodeResponseJson()['user']['company'];
        $address2 = $response->decodeResponseJson()['user']['addresses'][0];
        $this->assertEquals($company['name'], $company2['name']);
        $this->assertEquals($company['country_id'], $address2['country_id']);
    }

    /** @test */
    public function testGetOnboardingDataAfterCompanyUpdate()
    {
        $response = $this->json('GET', 'api/admin/onboarding');

        $response->assertOk();

        $status = $response->decodeResponseJson()['profile_complete'];
        $this->assertEquals($status, '2');
    }

    /** @test */
    public function testUpdateCompanySettings()
    {
        $settings = [
            'currency'           => 1,
            'time_zone'          => 'Asia/Kolkata',
            'language'           => 'en',
            'fiscal_year'        => '1-12',
            'carbon_date_format' => 'Y/m/d',
            'moment_date_format' => 'YYYY/MM/DD'
        ];

        $response = $this->json('POST', 'api/admin/onboarding/settings', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetOnboardingDataAfterSettingsUpdate()
    {
        $response = $this->json('GET', 'api/admin/onboarding');

        $response->assertOk();

        $status = $response->decodeResponseJson()['profile_complete'];
        $this->assertEquals($status, 'COMPLETED');
    }
}
