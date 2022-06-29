<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Crater\Models\Setting;
use Crater\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade;
use Vinkla\Hashids\Facades\Hashids;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (empty(User::count())) {
            $user = User::create([
                'email' => env('ADMIN_EMAIL', 'admin@craterapp.com'),
                'name' => env('ADMIN_NAME', 'Jane Doe'),
                'role' => 'super admin',
                'password' => env('ADMIN_PASSWORD', 'crater@123'),
            ]);

            $company = Company::create([
                'name' => env('COMPANY_NAME', 'xyz'),
                'owner_id' => $user->id,
                'slug' => env('COMPANY_SLUG', 'xyz'),
            ]);

            $company->unique_hash = Hashids::connection(Company::class)->encode($company->id);
            $company->save();
            $company->setupDefaultData();
            $user->companies()->attach($company->id);
            BouncerFacade::scope()->to($company->id);

            $user->assign('super admin');

            Setting::setSetting('profile_complete', 0);
        }
    }
}
