<?php

namespace Database\Factories;

use Crater\Models\Currency;
use Crater\Models\Customer;
use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
