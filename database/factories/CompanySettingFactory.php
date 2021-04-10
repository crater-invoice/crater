<?php

namespace Database\Factories;

use Crater\Models\CompanySetting;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanySettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanySetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'option' => $this->faker->word,
            'value' => $this->faker->word,
            'company_id' => User::where('role', 'super admin')->first()->company_id,
        ];
    }
}
