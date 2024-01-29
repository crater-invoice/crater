<?php

use Illuminate\Database\Migrations\Migration;
use InvoiceShelf\Models\CompanySetting;
use InvoiceShelf\Models\Expense;
use InvoiceShelf\Models\User;

class CalculateBaseValuesForExpenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where('role', 'super admin')->first();
        if ($user) {
            $companyId = $user->companies()->first()->id;

            $currency_id = CompanySetting::getSetting('currency', $companyId);

            $expenses = Expense::where('company_id', $companyId)->where('currency_id', null)->get();
            if ($expenses) {
                $expenses->map(function ($expense) use ($currency_id) {
                    $expense->update([
                        'currency_id' => $currency_id,
                        'exchange_rate' => 1,
                        'base_amount' => $expense->amount,
                    ]);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
