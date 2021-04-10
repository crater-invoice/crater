<?php

namespace Database\Factories;

use Crater\Models\InvoiceTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'path' => $this->faker->word,
            'view' => $this->faker->word,
            'name' => $this->faker->word,
        ];
    }
}
