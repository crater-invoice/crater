<?php

use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\FileDisk;
use Crater\Models\Invoice;
use Crater\Models\Item;
use Crater\Models\Payment;
use Crater\Models\Setting;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;

class UpdateCraterVersion400 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // seed the file disk
        $this->fileDiskSeed();

        Setting::setSetting('version', '4.0.0');

        $user = User::where('role', 'admin')->first();

        if ($user && $user->role == 'admin') {
            $user->update([
                'role' => 'super admin',
            ]);

            // Update language
            $user->setSettings(['language' => CompanySetting::getSetting('language', $user->company_id)]);

            // Update user's addresses
            if ($user->addresses()->exists()) {
                foreach ($user->addresses as $address) {
                    $address->company_id = $user->company_id;
                    $address->user_id = null;
                    $address->save();
                }
            }

            // Update company settings
            $this->updateCompanySettings($user);

            // Update Creator
            $this->updateCreatorId($user);
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

    private function fileDiskSeed()
    {
        $privateDisk = [
            'root' => config('filesystems.disks.local.root'),
            'driver' => 'local',
        ];

        $publicDisk = [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ];

        FileDisk::create([
            'credentials' => json_encode($publicDisk),
            'name' => 'local_public',
            'type' => 'SYSTEM',
            'driver' => 'local',
            'set_as_default' => false,
        ]);

        FileDisk::create([
            'credentials' => json_encode($privateDisk),
            'name' => 'local_private',
            'type' => 'SYSTEM',
            'driver' => 'local',
            'set_as_default' => true,
        ]);
    }

    private function updateCreatorId($user)
    {
        Invoice::where('company_id', '<>', null)->update(['creator_id' => $user->id]);
        Estimate::where('company_id', '<>', null)->update(['creator_id' => $user->id]);
        Expense::where('company_id', '<>', null)->update(['creator_id' => $user->id]);
        Payment::where('company_id', '<>', null)->update(['creator_id' => $user->id]);
        Item::where('company_id', '<>', null)->update(['creator_id' => $user->id]);
        User::where('role', 'customer')->update(['creator_id' => $user->id]);
    }

    private function updateCompanySettings($user)
    {
        $defaultInvoiceEmailBody = 'You have received a new invoice from <b>{COMPANY_NAME}</b>.</br> Please download using the button below:';
        $defaultEstimateEmailBody = 'You have received a new estimate from <b>{COMPANY_NAME}</b>.</br> Please download using the button below:';
        $defaultPaymentEmailBody = 'Thank you for the payment.</b></br> Please download your payment receipt using the button below:';
        $billingAddressFormat = '<h3>{BILLING_ADDRESS_NAME}</h3><p>{BILLING_ADDRESS_STREET_1}</p><p>{BILLING_ADDRESS_STREET_2}</p><p>{BILLING_CITY}  {BILLING_STATE}</p><p>{BILLING_COUNTRY}  {BILLING_ZIP_CODE}</p><p>{BILLING_PHONE}</p>';
        $shippingAddressFormat = '<h3>{SHIPPING_ADDRESS_NAME}</h3><p>{SHIPPING_ADDRESS_STREET_1}</p><p>{SHIPPING_ADDRESS_STREET_2}</p><p>{SHIPPING_CITY}  {SHIPPING_STATE}</p><p>{SHIPPING_COUNTRY}  {SHIPPING_ZIP_CODE}</p><p>{SHIPPING_PHONE}</p>';
        $companyAddressFormat = '<h3><strong>{COMPANY_NAME}</strong></h3><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_ADDRESS_STREET_2}</p><p>{COMPANY_CITY} {COMPANY_STATE}</p><p>{COMPANY_COUNTRY}  {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p>';
        $paymentFromCustomerAddress = '<h3>{BILLING_ADDRESS_NAME}</h3><p>{BILLING_ADDRESS_STREET_1}</p><p>{BILLING_ADDRESS_STREET_2}</p><p>{BILLING_CITY} {BILLING_STATE} {BILLING_ZIP_CODE}</p><p>{BILLING_COUNTRY}</p><p>{BILLING_PHONE}</p>';

        $settings = [
            'invoice_auto_generate' => 'YES',
            'payment_auto_generate' => 'YES',
            'estimate_auto_generate' => 'YES',
            'save_pdf_to_disk' => 'NO',
            'invoice_mail_body' => $defaultInvoiceEmailBody,
            'estimate_mail_body' => $defaultEstimateEmailBody,
            'payment_mail_body' => $defaultPaymentEmailBody,
            'invoice_company_address_format' => $companyAddressFormat,
            'invoice_shipping_address_format' => $shippingAddressFormat,
            'invoice_billing_address_format' => $billingAddressFormat,
            'estimate_company_address_format' => $companyAddressFormat,
            'estimate_shipping_address_format' => $shippingAddressFormat,
            'estimate_billing_address_format' => $billingAddressFormat,
            'payment_company_address_format' => $companyAddressFormat,
            'payment_from_customer_address_format' => $paymentFromCustomerAddress,
        ];

        CompanySetting::setSettings($settings, $user->company_id);
    }
}
