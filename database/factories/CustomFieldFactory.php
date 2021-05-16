<?php

namespace Database\Factories;

use Crater\Models\CustomField;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomFieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomField::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'label' => $this->faker->name,
            'order' => $this->faker->randomDigitNotNull,
            'is_required' => $this->faker->randomElement([true, false]),
            'model_type' => $this->faker->randomElement(['Customer', 'Invoice', 'Estimate', 'Expense', 'Payment']),
            'slug' => function (array $item) {
                return clean_slug($item['model_type'], $item['label']);
            },
            'type' => $this->faker->randomElement(['Text', 'Textarea', 'Phone', 'URL', 'Number','Dropdown' , 'Switch', 'Date', 'DateTime', 'Time']),
            'company_id' => User::where('role', 'super admin')->first()->company_id,
        ];
    }
}
