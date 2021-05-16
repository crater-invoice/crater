<?php

namespace Database\Seeders;

use Crater\Models\Address;
use Crater\Models\Setting;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('role', 'super admin')->first();

        $user->setSettings(['language' => 'en']);

        Address::create(['company_id' => $user->company_id, 'country_id' => 1]);

        Setting::setSetting('profile_complete', 'COMPLETED');

        \Storage::disk('local')->put('database_created', 'database_created');
    }
}
