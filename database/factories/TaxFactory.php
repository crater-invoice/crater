<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use InvoiceShelf\Models\Currency;
use InvoiceShelf\Models\Tax;
use InvoiceShelf\Models\TaxType;
use InvoiceShelf\Models\User;

class TaxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tax::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tax_type_id' => TaxType::factory(),
            'percent' => function (array $item) {
                return TaxType::find($item['tax_type_id'])->percent;
            },
            'name' => function (array $item) {
                return TaxType::find($item['tax_type_id'])->name;
            },
            'company_id' => User::find(1)->companies()->first()->id,
            'amount' => $this->faker->randomDigitNotNull,
            'compound_tax' => $this->faker->randomDigitNotNull,
            'base_amount' => $this->faker->randomDigitNotNull,
            'currency_id' => Currency::where('name', 'US Dollar')->first()->id,
        ];
    }
}
