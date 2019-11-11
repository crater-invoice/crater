<?php

use Illuminate\Database\Seeder;
use Laraspace\User;
use Laraspace\Setting;
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
        User::create([
            'email' => 'admin@crater.in',
            'name' => 'Jane Doe',
            'role' => 'admin',
            'password' => Hash::make('admin@123')
        ]);

        Setting::setSetting('profile_complete', 0);
    }
}
