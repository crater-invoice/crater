<?php

namespace Database\Factories;

use Crater\Models\TaxType;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaxType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'company_id' => User::find(1)->companies()->first()->id,
            'percent' => $this->faker->numberBetween($min = 0, $max = 100),
            'description' => $this->faker->text,
            'compound_tax' => 0,
            'collective_tax' => 0,
        ];
    }
}
