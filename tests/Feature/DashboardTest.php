<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Laravel\Passport\Passport;
use SettingsSeeder;

class DashboardTest extends TestCase
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
    public function testDashboard()
    {
        $response = $this->json('GET', 'api/dashboard');

        $response->assertOk();
    }

    /** @test */
    public function testPieChartData()
    {
        $response = $this->json('GET', 'api/dashboard/expense/chart');

        $response->assertOk();
    }
}
