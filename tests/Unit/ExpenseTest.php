<?php

use Crater\Models\Expense;
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

test('expense belongs to category', function () {
    $expense = Expense::factory()->forCategory()->create();

    $this->assertTrue($expense->category()->exists());
});

test('expense belongs to user', function () {
    $expense = Expense::factory()->forUser()->create();

    $this->assertTrue($expense->user()->exists());
});
