<?php

use Crater\Http\Controllers\V1\Settings\CompanyController;
use Crater\Http\Requests\CompanyRequest;
use Crater\Http\Requests\ProfileRequest;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('get profile', function () {
    $response = getJson('api/v1/me');

    $response->assertOk();
});


test('update profile using a form request', function () {
    $this->assertActionUsesFormRequest(
        CompanyController::class,
        'updateProfile',
        ProfileRequest::class
    );
});

test('update profile', function () {
    $user = [
        'name' => 'John Doe',
        'password' => 'admin@123',
        'email' => 'admin@crater.in',
    ];

    $response = putJson('api/v1/me', $user);

    $response->assertOk();

    $this->assertDatabaseHas('users', [
        'name' => $user['name'],
        'email' => $user['email'],
    ]);
});

test('update company using a form request', function () {
    $this->assertActionUsesFormRequest(
        CompanyController::class,
        'updateCompany',
        CompanyRequest::class
    );
});

test('update company', function () {
    $company = [
        'name' => 'XYZ',
        'country_id' => 2,
        'state' => 'city',
        'city' => 'state',
        'address_street_1' => 'test1',
        'address_street_2' => 'test2',
        'phone' => '1234567890',
        'zip' => '112233',
    ];

    $response = putJson('api/v1/company', $company);

    $response->assertOk();

    $this->assertDatabaseHas('companies', [
        'name' => $company['name'],
    ]);

    $this->assertDatabaseHas('addresses', [
        'country_id' => $company['country_id'],
        'state' => $company['state'],
        'city' => $company['city'],
        'address_street_1' => $company['address_street_1'],
        'address_street_2' => $company['address_street_2'],
        'phone' => $company['phone'],
        'zip' => $company['zip'],
    ]);
});

test('update settings', function () {
    $settings = [
        'currency' => 1,
        'time_zone' => 'Asia/Kolkata',
        'language' => 'en',
        'fiscal_year' => '1-12',
        'carbon_date_format' => 'Y/m/d',
        'moment_date_format' => 'YYYY/MM/DD',
        'notification_email' => 'noreply@crater.in',
        'notify_invoice_viewed' => 'YES',
        'notify_estimate_viewed' => 'YES',
        'tax_per_item' => 'YES',
        'discount_per_item' => 'YES',
    ];

    $response = postJson('/api/v1/company/settings', ['settings' => $settings]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    foreach ($settings as $key => $value) {
        $this->assertDatabaseHas('company_settings', [
            'option' => $key,
            'value' => $value,
        ]);
    }
});

test('get notification email settings', function () {
    $data['settings'] = [
        'currency',
        'time_zone',
        'language',
        'fiscal_year',
        'carbon_date_format',
        'moment_date_format',
        'notification_email',
        'notify_invoice_viewed',
        'notify_estimate_viewed',
        'tax_per_item',
        'discount_per_item',
    ];

    $response = getJson('/api/v1/company/settings?'.http_build_query($data));

    $response->assertOk();
});
