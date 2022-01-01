<?php

namespace Database\Factories;

use Crater\Models\Currency;
use Crater\Models\Customer;
use Crater\Models\Estimate;
use Crater\Models\User;
use Crater\Services\SerialNumberFormatter;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstimateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estimate::class;

    public function sent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Estimate::STATUS_SENT,
            ];
        });
    }

    public function viewed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Estimate::STATUS_VIEWED,
            ];
        });
    }

    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Estimate::STATUS_EXPIRED,
            ];
        });
    }

    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Estimate::STATUS_ACCEPTED,
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Estimate::STATUS_REJECTED,
            ];
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sequenceNumber = (new SerialNumberFormatter())
            ->setModel(new Estimate())
            ->setCompany(User::find(1)->companies()->first()->id)
            ->setNextNumbers();

        return [
            'estimate_date' => $this->faker->date('Y-m-d', 'now'),
            'expiry_date' => $this->faker->date('Y-m-d', 'now'),
            'estimate_number' => $sequenceNumber->getNextNumber(),
            'sequence_number' => $sequenceNumber->nextSequenceNumber,
            'customer_sequence_number' => $sequenceNumber->nextCustomerSequenceNumber,
            'reference_number' => $sequenceNumber->getNextNumber(),
            'company_id' => User::find(1)->companies()->first()->id,
            'status' => Estimate::STATUS_DRAFT,
            'template_name' => 'estimate1',
            'sub_total' => $this->faker->randomDigitNotNull,
            'total' => $this->faker->randomDigitNotNull,
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_val' => function (array $estimate) {
                return $estimate['discount_type'] == 'percentage' ? $this->faker->numberBetween($min = 0, $max = 100) : $this->faker->randomDigitNotNull;
            },
            'discount' => function (array $estimate) {
                return $estimate['discount_type'] == 'percentage' ? (($estimate['discount_val'] * $estimate['total']) / 100) : $estimate['discount_val'];
            },
            'tax_per_item' => 'YES',
            'discount_per_item' => 'No',
            'tax' => $this->faker->randomDigitNotNull,
            'notes' => $this->faker->text(80),
            'unique_hash' => str_random(60),
            'customer_id' => Customer::factory(),
            'exchange_rate' => $this->faker->randomDigitNotNull,
            'base_discount_val' => $this->faker->randomDigitNotNull,
            'base_sub_total' => $this->faker->randomDigitNotNull,
            'base_total' => $this->faker->randomDigitNotNull,
            'base_tax' => $this->faker->randomDigitNotNull,
            'currency_id' => Currency::find(1)->id,
        ];
    }
}
