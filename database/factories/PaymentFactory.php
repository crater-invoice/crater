<?php

namespace Database\Factories;

use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'company_id' => User::where('role', 'super admin')->first()->company_id,
            'payment_date' => $this->faker->date('Y-m-d', 'now'),
            'notes' => $this->faker->text(80),
            'amount' => $this->faker->randomDigitNotNull,
            'payment_number' => 'PAY-'.Payment::getNextPaymentNumber('PAY'),
        ];
    }
}
