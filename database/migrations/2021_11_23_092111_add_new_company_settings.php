<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Database\Migrations\Migration;

class AddNewCompanySettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = Company::all();

        if ($companies) {
            $companies->map(function ($company) {
                $settingsToRemove = [
                    'invoice_number_length',
                    'estimate_number_length',
                    'payment_number_length',
                    'invoice_prefix',
                    'estimate_prefix',
                    'payment_prefix',
                ];

                $oldSettings = CompanySetting::getSettings($settingsToRemove, $company->id);
                $oldSettings = $oldSettings->toArray();

                $settings = [
                    'invoice_set_due_date_automatically' => 'YES',
                    'invoice_due_date_days' => 7,
                    'estimate_set_expiry_date_automatically' => 'YES',
                    'estimate_expiry_date_days' => 7,
                    'estimate_convert_action' => 'no_action',
                    'bulk_exchange_rate_configured' => "NO",
                    'invoice_number_format' => "{{SERIES:{$oldSettings['invoice_prefix']}}}{{DELIMITER:-}}{{SEQUENCE:{$oldSettings['invoice_number_length']}}}",
                    'estimate_number_format' => "{{SERIES:{$oldSettings['estimate_prefix']}}}{{DELIMITER:-}}{{SEQUENCE:{$oldSettings['estimate_number_length']}}}",
                    'payment_number_format' => "{{SERIES:{$oldSettings['payment_prefix']}}}{{DELIMITER:-}}{{SEQUENCE:{$oldSettings['payment_number_length']}}}",
                ];

                CompanySetting::whereIn('option', $settingsToRemove)->delete();

                CompanySetting::setSettings($settings, $company->id);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
