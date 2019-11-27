<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Laravel\Passport\Passport;
use SettingsSeeder;

class CompanySettingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(SettingsSeeder::class);
        $user = User::find(1);
        $this->withHeaders([
            'company' => $user->company_id,
        ]);
        Passport::actingAs(
            $user,
            ['*']
        );
    }

    /** @test */
    public function testGetProfile()
    {
        $response = $this->json('GET', 'api/settings/profile');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateProfile()
    {
        $user = [
            'name' => 'John Doe',
            'password' => 'admin@123',
            'email' => 'admin@crater.in'
        ];

        $response = $this->json('PUT', 'api/settings/profile', $user);

        $response->assertOk();
    }

    /** @test */
    public function testGetUpdateCompanyDetails()
    {
        $response = $this->json('GET', 'api/settings/company');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateCompany()
    {
        $company = [
            'name' => 'XYZ',
            'country_id' => 2,
            'state' => 'city',
            'city' => 'state',
            'address_street_1' => 'test1',
            'address_street_2' => 'test2',
            'phone' => '1234567890',
            'zip' => '112233'
        ];

        $response = $this->json('POST', 'api/settings/company', $company);

        $response->assertOk();
        $company2 = $response->decodeResponseJson()['user']['company'];
        $address2 = $response->decodeResponseJson()['user']['addresses'][0];
        $this->assertEquals($company['name'], $company2['name']);
        $this->assertEquals($company['country_id'], $address2['country_id']);
        $this->assertEquals($company['state'], $address2['state']);
        $this->assertEquals($company['city'], $address2['city']);
        $this->assertEquals($company['address_street_1'], $address2['address_street_1']);
        $this->assertEquals($company['address_street_2'], $address2['address_street_2']);
        $this->assertEquals($company['phone'], $address2['phone']);
        $this->assertEquals($company['zip'], $address2['zip']);
    }

    /** @test */
    public function testGetSettings()
    {
        $response = $this->json('GET', 'api/settings/general');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateSettings()
    {
        $settings = [
            'currency'           => 1,
            'time_zone'          => 'Asia/Kolkata',
            'language'           => 'en',
            'fiscal_year'        => '1-12',
            'carbon_date_format' => 'Y/m/d',
            'moment_date_format' => 'YYYY/MM/DD'
        ];

        $response = $this->json('PUT', 'api/settings/general', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testUpdateNotificationEmailSettings()
    {
        $settings = [
            'key' => 'notification_email',
            'value' => 'noreply@crater.in'
        ];

        $response = $this->json('PUT', 'api/settings/update-setting', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetNotificationEmailSettings()
    {
        $response = $this->json('GET', 'api/settings/get-setting?key=notification_email');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateNotificationInvoiceViewedSettings()
    {
        $settings = [
            'key' => 'notify_invoice_viewed',
            'value' => 'YES'
        ];

        $response = $this->json('PUT', 'api/settings/update-setting', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetNotificationInvoiceViewedSettings()
    {
        $response = $this->json('GET', 'api/settings/get-setting?key=notify_invoice_viewed');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateNotificationEstimateViewedSettings()
    {
        $settings = [
            'key' => 'notify_estimate_viewed',
            'value' => 'YES'
        ];

        $response = $this->json('PUT', 'api/settings/update-setting', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetNotificationEstimateViewedSettings()
    {
        $response = $this->json('GET', 'api/settings/get-setting?key=notify_estimate_viewed');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateTaxPerItemSetting()
    {
        $settings = [
            'key' => 'tax_per_item',
            'value' => 'YES'
        ];

        $response = $this->json('PUT', 'api/settings/update-setting', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetTaxPerItemSetting()
    {
        $response = $this->json('GET', 'api/settings/get-setting?key=tax_per_item');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateDiscountPerItemSetting()
    {
        $settings = [
            'key' => 'discount_per_item',
            'value' => 'YES'
        ];

        $response = $this->json('PUT', 'api/settings/update-setting', $settings);

        $response->assertOk();
    }

    /** @test */
    public function testGetDiscountPerItemSetting()
    {
        $response = $this->json('GET', 'api/settings/get-setting?key=discount_per_item');

        $response->assertOk();
    }
}
