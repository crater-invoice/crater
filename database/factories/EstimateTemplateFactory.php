<?php

namespace Database\Factories;

use Crater\Models\EstimateTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstimateTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EstimateTemplate::class;

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
