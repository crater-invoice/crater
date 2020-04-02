<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Crater\User;
use Crater\Company;
use Laravel\Passport\Passport;
use SettingsSeeder;

class ReportTest extends TestCase
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
    public function testGetCustomerSalesReport()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'from_date' => '01/02/2019',
            'to_date' => '10/02/2019',
        ];
        $queryString = http_build_query($filters, '', '&');
        $queryString = Company::find(1)->unique_hash . '?' . $queryString;

        $response = $this->json('GET', 'reports/sales/customers/'. $queryString);

        $response->assertOk();
    }

    /** @test */
    public function testGetItemSalesReport()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'from_date' => '01/02/2019',
            'to_date' => '10/02/2019',
        ];
        $queryString = http_build_query($filters, '', '&');
        $queryString = Company::find(1)->unique_hash . '?' . $queryString;

        $response = $this->json('GET', 'reports/sales/items/' . $queryString);

        $response->assertOk();
    }

    /** @test */
    public function testGetExpensesReport()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'from_date' => '01/02/2019',
            'to_date' => '10/02/2019',
        ];
        $queryString = http_build_query($filters, '', '&');
        $queryString = Company::find(1)->unique_hash . '?' . $queryString;

        $response = $this->json('GET', 'reports/expenses/' . $queryString);

        $response->assertOk();
    }

    /** @test */
    public function testGetTaxSummary()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'from_date' => '01/02/2019',
            'to_date' => '10/02/2019',
        ];
        $queryString = http_build_query($filters, '', '&');
        $queryString = Company::find(1)->unique_hash . '?' . $queryString;

        $response = $this->json('GET', 'reports/tax-summary/' . $queryString);

        $response->assertOk();
    }

    /** @test */
    public function testGetProfitLoss()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'from_date' => '01/02/2019',
            'to_date' => '10/02/2019',
        ];
        $queryString = http_build_query($filters, '', '&');
        $queryString = Company::find(1)->unique_hash . '?' . $queryString;

        $response = $this->json('GET', 'reports/profit-loss/' . $queryString);

        $response->assertOk();
    }
}
