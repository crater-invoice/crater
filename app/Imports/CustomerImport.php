<?php

namespace Crater\Imports;

use Crater\Models\Address;
use Crater\Models\Country;
use Crater\Models\Currency;
use Crater\Models\Customer;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class CustomerImport implements OnEachRow, WithHeadingRow
{
    private $userId;
    private $companyId;

    public function __construct(
        $userId,
        $companyId
    )
    {
        $this->userId = $userId;
        $this->companyId = $companyId;
    }

    public function onRow(Row $row)
    {
        $currency = Currency::where('code', $row['currency'])->first();
        $customer = new Customer([
            'creator_id' => $this->userId,
            'company_id' => $this->companyId,

            'name' => $row['name'],
            'email' => $row['email'] ?? null,
            'currency_id' => $currency->id ?? null,
            'password' => $row['password'] ?? null,
            'phone' => $row['phone'] ?? null,
            'prefix' => $row['prefix'] ?? null,
            'company_name' => $row['company_name'] ?? null,
            'contact_name' => $row['primary_contact_name'] ?? null,
            'website' => $row['website'] ?? null,
            'enable_portal' => boolval($row['enable_portal'] ?? null),
            'estimate_prefix' => $row['estimate_prefix'] ?? null,
            'payment_prefix' => $row['payment_prefix'] ?? null,
            'invoice_prefix' => $row['invoice_prefix'] ?? null,
        ]);

        $billingCountry = Country::where('name', $row['billing_country'])->orWhere('code', $row['billing_country'])->first();
        $billingAddress = new Address([
            'type' => Address::BILLING_TYPE,
            'name' => $row['billing_name'] ?? null,
            'address_street_1' => $row['billing_address_street_1'] ?? null,
            'address_street_2' => $row['billing_address_street_2'] ?? null,
            'city' => $row['billing_city'] ?? null,
            'state' => $row['billing_state'] ?? null,
            'country_id' => $billingCountry->id ?? null,
            'zip' => $row['billing_zip'] ?? null,
            'phone' => $row['billing_phone'] ?? null,
            'fax' => $row['billing_fax'] ?? null,
        ]);

        $shippingCountry = Country::where('name', $row['shipping_country'])->orWhere('code', $row['shipping_country'])->first();
        $shippingAddress = new Address([
            'type' => Address::SHIPPING_TYPE,
            'name' => $row['shipping_name'] ?? null,
            'address_street_1' => $row['shipping_address_street_1'] ?? null,
            'address_street_2' => $row['shipping_address_street_2'] ?? null,
            'city' => $row['shipping_city'] ?? null,
            'state' => $row['shipping_state'] ?? null,
            'country_id' => $shippingCountry->id ?? null,
            'zip' => $row['shipping_zip'] ?? null,
            'phone' => $row['shipping_phone'] ?? null,
            'fax' => $row['shipping_fax'] ?? null,
        ]);

        $customer->save();
        $customer->billingAddress()->save($billingAddress);
        $customer->shippingAddress()->save($shippingAddress);
    }
}
