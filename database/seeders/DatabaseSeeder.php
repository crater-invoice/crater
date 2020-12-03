<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(DefaultSettingsSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(EstimateTemplateSeeder::class);
        $this->call(InvoiceTemplateSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(UnitSeeder::class);
    }
}
