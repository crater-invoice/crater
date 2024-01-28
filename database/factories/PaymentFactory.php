<?php

namespace Database\Factories;

use InvoiceShelf\Models\Currency;
use InvoiceShelf\Models\Customer;
use InvoiceShelf\Models\Payment;
use InvoiceShelf\Models\PaymentMethod;
use InvoiceShelf\Models\User;
use InvoiceShelf\Services\SerialNumberFormatter;
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
        $sequenceNumber = (new SerialNumberFormatter())
            ->setModel(new Payment())
            ->setCompany(User::find(1)->companies()->first()->id)
            ->setNextNumbers();

        return [
            'company_id' => User::find(1)->companies()->first()->id,
            'payment_date' => $this->faker->date('Y-m-d', 'now'),
            'notes' => $this->faker->text(80),
            'amount' => $this->faker->randomDigitNotNull,
            'sequence_number' => $sequenceNumber->nextSequenceNumber,
            'customer_sequence_number' => $sequenceNumber->nextCustomerSequenceNumber,
            'payment_number' => $sequenceNumber->getNextNumber(),
            'unique_hash' => str_random(60),
            'payment_method_id' => PaymentMethod::find(1)->id,
            'customer_id' => Customer::factory(),
            'base_amount' => $this->faker->randomDigitNotNull,
            'currency_id' => Currency::find(1)->id,
        ];
    }
}
