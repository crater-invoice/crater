<?php

namespace Database\Factories;

use Crater\Models\Address;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'address_street_1' => $this->faker->streetAddress,
            'address_street_2' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country_id' => 231,
            'zip' => $this->faker->postcode,
            'phone' => $this->faker->phoneNumber,
            'fax' => $this->faker->phoneNumber,
            'type' => $this->faker->randomElement([Address::BILLING_TYPE, Address::SHIPPING_TYPE]),
            'user_id' => User::factory(),
        ];
    }
}
