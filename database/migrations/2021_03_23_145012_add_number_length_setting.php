<?php

use Crater\Models\CompanySetting;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;

class AddNumberLengthSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where('role', 'super admin')->first();

        if ($user) {
            $invoice_number_length = CompanySetting::getSetting('invoice_number_length', $user->company_id);
            if (empty($invoice_number_length)) {
                CompanySetting::setSettings(['invoice_number_length' => '6'], $user->company_id);
            }

            $estimate_number_length = CompanySetting::getSetting('estimate_number_length', $user->company_id);
            if (empty($estimate_number_length)) {
                CompanySetting::setSettings(['estimate_number_length' => '6'], $user->company_id);
            }

            $payment_number_length = CompanySetting::getSetting('payment_number_length', $user->company_id);
            if (empty($payment_number_length)) {
                CompanySetting::setSettings(['payment_number_length' => '6'], $user->company_id);
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
        //
    }
}
