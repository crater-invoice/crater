<?php

namespace Database\Factories;

use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    public function sent()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_SENT,
            ];
        });
    }

    public function viewed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_VIEWED,
            ];
        });
    }

    public function overdue()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_OVERDUE,
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_COMPLETED,
            ];
        });
    }

    public function due()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_DUE,
            ];
        });
    }

    public function unpaid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_UNPAID,
            ];
        });
    }

    public function partiallyPaid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_PARTIALLY_PAID,
            ];
        });
    }

    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_PAID,
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
        return [
            'invoice_date' => $this->faker->date('Y-m-d', 'now'),
            'due_date' => $this->faker->date('Y-m-d', 'now'),
            'invoice_number' => 'INV-'.Invoice::getNextInvoiceNumber('INV'),
            'reference_number' => Invoice::getNextInvoiceNumber('INV'),
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'template_name' => 'invoice1',
            'status' => Invoice::STATUS_DRAFT,
            'tax_per_item' => 'NO',
            'discount_per_item' => 'NO',
            'paid_status' => Invoice::STATUS_UNPAID,
            'company_id' => User::where('role', 'super admin')->first()->company_id,
            'sub_total' => $this->faker->randomDigitNotNull,
            'total' => $this->faker->randomDigitNotNull,
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_val' => function (array $invoice) {
                return $invoice['discount_type'] == 'percentage' ? $this->faker->numberBetween($min = 0, $max = 100) : $this->faker->randomDigitNotNull;
            },
            'discount' => function (array $invoice) {
                return $invoice['discount_type'] == 'percentage' ? (($invoice['discount_val'] * $invoice['total']) / 100) : $invoice['discount_val'];
            },
            'tax' => $this->faker->randomDigitNotNull,
            'due_amount' => function (array $invoice) {
                return $invoice['total'];
            },
            'notes' => $this->faker->text(80),
            'unique_hash' => str_random(60),
        ];
    }
}
