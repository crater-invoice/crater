<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Illuminate\Database\Seeder;
use Crater\Models\User;
use Crater\Models\Setting;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'admin@craterapp.com',
            'name' => 'Jane Doe',
            'role' => 'super admin',
            'password' => 'crater@123'
        ]);

        $company = Company::create([
            'name' => 'xyz',
            'unique_hash' => str_random(20)
        ]);

        $user->company_id = $company->id;
        $user->save();

        Setting::setSetting('profile_complete', 0);
    }
}
