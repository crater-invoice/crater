<?php

namespace Database\Factories;

use Crater\Models\Customer;
use Crater\Models\RecurringInvoice;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RecurringInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'starts_at' => $this->faker->iso8601(),
            'send_automatically' => false,
            'status' => $this->faker->randomElement(['COMPLETED', 'ON_HOLD', 'ACTIVE']),
            'tax_per_item' => 'NO',
            'discount_per_item' => 'NO',
            'sub_total' => $this->faker->randomDigitNotNull,
            'total' => $this->faker->randomDigitNotNull,
            'tax' => $this->faker->randomDigitNotNull,
            'due_amount' => $this->faker->randomDigitNotNull,
            'discount' => $this->faker->randomDigitNotNull,
            'discount_val' => $this->faker->randomDigitNotNull,
            'customer_id' => Customer::factory(),
            'company_id' => User::find(1)->companies()->first()->id,
            'frequency' => '* * 18 * *',
            'limit_by' => $this->faker->randomElement(['NONE', 'COUNT', 'DATE']),
            'limit_count' => $this->faker->randomDigit,
            'limit_date' => $this->faker->date(),
            'exchange_rate' => $this->faker->randomDigitNotNull
        ];
    }
}
