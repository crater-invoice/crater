<?php

use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceColumn extends Migration
{
    const DEPRECATED_FIELDS = [
        'invoice_number_length' => 6,
        'estimate_number_length' => 6,
        'payment_number_length' => 6,
        'invoice_prefix' => 'INV',
        'estimate_prefix' => 'EST',
        'payment_prefix' => 'PAY',
    ];
    const NEW_SETTINGS_FIELDS = [
        'invoice_format' => '{{SERIES:INV}}{{DELIMITER:-}}{{SEQUENCE:6}}',
        'estimate_format' => '{{SERIES:EST}}{{DELIMITER:-}}{{SEQUENCE:6}}',
        'payment_format' => '{{SERIES:PAY}}{{DELIMITER:-}}{{SEQUENCE:6}}',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('invoices', 'sequence_number')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->mediumInteger('sequence_number')->unsigned()->after('id');
            });
        }
        if (!Schema::hasColumn('estimates', 'sequence_number')) {
            Schema::table('estimates', function (Blueprint $table) {
                $table->mediumInteger('sequence_number')->unsigned()->after('id');
            });
        }
        if (!Schema::hasColumn('payments', 'sequence_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->mediumInteger('sequence_number')->unsigned()->after('id');
            });
        }

        $user = User::where('role', 'super admin')->first();

        if (Schema::hasColumn('invoices', 'sequence_number')) {
            $invoices = Invoice::all();

            $invoices->map(function ($invoice) {
                $invoiceNumber = explode("-", $invoice->invoice_number);
                $invoice->sequence_number = intval(end($invoiceNumber));
                $invoice->save();
            });
        }

        if (Schema::hasColumn('estimates', 'sequence_number')) {
            $estimates = Estimate::all();

            $estimates->map(function ($estimate) {
                $estimateNumber = explode("-", $estimate->estimate_number);
                $estimate->sequence_number = intval(end($estimateNumber));
                $estimate->save();
            });
        }

        if (Schema::hasColumn('payments', 'sequence_number')) {
            $payments = Payment::all();

            $payments->map(function ($payment) {
                $paymentNumber = explode("-", $payment->payment_number);
                $payment->sequence_number = intval(end($paymentNumber));
                $payment->save();
            });
        }

        /**
         * Invoice/Est prefix, Inv/Est length
         */
        $this->updateCompanySettings($user, self::NEW_SETTINGS_FIELDS);
        $this->removeDeprecatedFields(array_keys(self::DEPRECATED_FIELDS));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('sequence_number');
        });
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropColumn('sequence_number');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('sequence_number');
        });

        $user = User::where('role', 'super admin')->first();

        $this->updateCompanySettings($user, self::DEPRECATED_FIELDS);
        $this->removeDeprecatedFields(array_keys(self::NEW_SETTINGS_FIELDS));
    }

    private function updateCompanySettings($user, $fields)
    {
        CompanySetting::setSettings($fields, $user->company_id);
    }

    private function removeDeprecatedFields(array $fields)
    {
        CompanySetting::whereIn('option',$fields)->delete();
    }
}
