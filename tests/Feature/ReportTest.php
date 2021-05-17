<?php

use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;

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

test('get customer sales report', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'from_date' => '2020-07-18',
        'to_date' => '2020-07-20',
    ];
    $queryString = http_build_query($filters, '', '&');
    $queryString = Company::find(1)->unique_hash.'?'.$queryString;

    $response = getJson('reports/sales/customers/'.$queryString);

    $response->assertOk();
});

test('get item sales report', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'from_date' => '2020-07-18',
        'to_date' => '2020-07-20',
    ];
    $queryString = http_build_query($filters, '', '&');
    $queryString = Company::find(1)->unique_hash.'?'.$queryString;

    $response = getJson('reports/sales/items/'.$queryString);

    $response->assertOk();
});

test('get expenses report', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'from_date' => '2020-07-18',
        'to_date' => '2020-07-20',
    ];
    $queryString = http_build_query($filters, '', '&');
    $queryString = Company::find(1)->unique_hash.'?'.$queryString;

    $response = getJson('reports/expenses/'.$queryString);

    $response->assertOk();
});

test('get tax summary', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'from_date' => '2020-07-18',
        'to_date' => '2020-07-20',
    ];
    $queryString = http_build_query($filters, '', '&');
    $queryString = Company::find(1)->unique_hash.'?'.$queryString;

    $response = getJson('reports/tax-summary/'.$queryString);

    $response->assertOk();
});

test('get profit loss', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'from_date' => '2020-07-18',
        'to_date' => '2020-07-20',
    ];
    $queryString = http_build_query($filters, '', '&');
    $queryString = Company::find(1)->unique_hash.'?'.$queryString;

    $response = getJson('reports/profit-loss/'.$queryString);

    $response->assertOk();
});
