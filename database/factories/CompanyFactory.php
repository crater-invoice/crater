<?php

namespace Database\Factories;

use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'unique_hash' => str_random(20),
            'name' => $this->faker->name(),
            'owner_id' => User::where('role', 'super admin')->first()->id,
            'slug' => $this->faker->word
        ];
    }
}
