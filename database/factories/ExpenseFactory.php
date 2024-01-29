<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InvoiceShelf\Models\Currency;
use InvoiceShelf\Models\Customer;
use InvoiceShelf\Models\Expense;
use InvoiceShelf\Models\ExpenseCategory;
use InvoiceShelf\Models\User;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expense_date' => $this->faker->date('Y-m-d', 'now'),
            'expense_category_id' => ExpenseCategory::factory(),
            'company_id' => User::find(1)->companies()->first()->id,
            'amount' => $this->faker->randomDigitNotNull,
            'notes' => $this->faker->text,
            'attachment_receipt' => null,
            'customer_id' => Customer::factory(),
            'exchange_rate' => $this->faker->randomDigitNotNull,
            'base_amount' => $this->faker->randomDigitNotNull,
            'currency_id' => Currency::find(1)->id,
        ];
    }
}
