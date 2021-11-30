<?php

namespace Database\Factories;

use Crater\Models\InvoiceItem;
use Crater\Models\Item;
use Crater\Models\RecurringInvoice;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'name' => function (array $item) {
                return Item::find($item['item_id'])->name;
            },
            'description' => function (array $item) {
                return Item::find($item['item_id'])->description;
            },
            'price' => function (array $item) {
                return Item::find($item['item_id'])->price;
            },
            'company_id' => User::find(1)->companies()->first()->id,
            'quantity' => $this->faker->randomDigitNotNull,
            'total' => function (array $item) {
                return ($item['price'] * $item['quantity']);
            },
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_val' => function (array $invoice) {
                return $invoice['discount_type'] == 'percentage' ? $this->faker->numberBetween($min = 0, $max = 100) : $this->faker->randomDigitNotNull;
            },
            'discount' => function (array $invoice) {
                return $invoice['discount_type'] == 'percentage' ? (($invoice['discount_val'] * $invoice['total']) / 100) : $invoice['discount_val'];
            },
            'tax' => $this->faker->randomDigitNotNull,
            'recurring_invoice_id' => RecurringInvoice::factory(),
            'exchange_rate' => $this->faker->randomDigitNotNull,
            'base_discount_val' => $this->faker->randomDigitNotNull,
            'base_price' => $this->faker->randomDigitNotNull,
            'base_total' => $this->faker->randomDigitNotNull,
            'base_tax' => $this->faker->randomDigitNotNull,
        ];
    }
}
