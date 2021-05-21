<?php

use Crater\Models\EstimateTemplate;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('estimate template has many estimates', function () {
    $estimateTemplate = EstimateTemplate::factory()->hasEstimates(5)->create();

    $this->assertCount(5, $estimateTemplate->estimates);

    $this->assertTrue($estimateTemplate->estimates()->exists());
});
