<?php

use Illuminate\Database\Seeder;
use Crater\Company;
use Crater\User;
use Crater\Address;
use Crater\CompanySetting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create(['name' => 'TEST', 'unique_hash' => str_random(60)]);

        $user = User::find(1);
        $user->company_id = $company->id;
        $user->save();

        $address = Address::create(['user_id' => $user->id, 'country_id' => 1]);

        $sets = [
            'currency'           => 1,
            'time_zone'          => 'UTC',
            'language'           => 'en',
            'notification_email' =>  $user->email,
            'fiscal_year'        => '1-12',
            'carbon_date_format' => 'd m Y',
            'moment_date_format' => 'DD MMM YYYY'
        ];

        foreach ($sets as $key => $value) {
            CompanySetting::setSetting(
                $key,
                $value,
                $company->id
            );
        }
    }
}
