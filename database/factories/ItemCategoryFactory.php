<?php

namespace Database\Factories;

use Crater\Models\ItemCategory;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ItemCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'number' => $this->faker->randomNumber(3),
            'company_id' => User::find(1)->companies()->first()->id,
        ];
    }
}
