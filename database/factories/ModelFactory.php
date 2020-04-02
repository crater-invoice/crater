<?php
use Illuminate\Support\Facades\Hash;
use Crater\Address;
use Crater\User;
use Crater\Currency;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'company_name' => $faker->company,
        'contact_name' => $faker->name,
        'website' => $faker->url,
        'enable_portal' => true,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'company_id' => User::find(1)->company_id,
        'role' => 'admin',
        'password' => $password ?: $password = Hash::make('secret'),
        'remember_token' => str_random(10),
        'currency_id' => Currency::first()->id
    ];
});

$factory->afterCreating(User::class, function ($user, $faker) {
    $user->addresses()->save(factory(Address::class)->make());
    $user->addresses()->save(factory(Address::class)->make(['type' => Address::SHIPPING_TYPE]));
});
