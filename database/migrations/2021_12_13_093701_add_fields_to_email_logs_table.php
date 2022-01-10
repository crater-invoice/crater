<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToEmailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->string('token')->unique()->nullable();
        });

        $user = User::where('role', 'super admin')->first();

        if ($user) {
            $settings = [
                'automatically_expire_public_links' => 'Yes',
                'link_expiry_days' => 7
            ];

            $companies = Company::all();

            foreach ($companies as $company) {
                CompanySetting::setSettings($settings, $company->id);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
}
