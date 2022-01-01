<?php

namespace Crater\Http\Requests;

use Crater\Models\CompanySetting;
use Crater\Models\Customer;
use Crater\Models\RecurringInvoice;
use Illuminate\Foundation\Http\FormRequest;

class RecurringInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $companyCurrency = CompanySetting::getSetting('currency', $this->header('company'));

        $rules = [
            'starts_at' => [
                'required'
            ],
            'send_automatically' => [
                'required',
                'boolean'
            ],
            'customer_id' => [
                'required'
            ],
            'exchange_rate' => [
                'nullable'
            ],
            'discount' => [
                'required',
            ],
            'discount_val' => [
                'required',
            ],
            'sub_total' => [
                'required',
            ],
            'total' => [
                'required',
            ],
            'tax' => [
                'required',
            ],
            'status' => [
                'required'
            ],
            'exchange_rate' => [
                'nullable'
            ],
            'frequency' => [
                'required'
            ],
            'limit_by' => [
                'required'
            ],
            'limit_count' => [
                'required_if:limit_by,COUNT',
            ],
            'limit_date' => [
                'required_if:limit_by,DATE',
            ],
            'items' => [
                'required'
            ],
            'items.*' => [
                'required'
            ]
        ];

        $customer = Customer::find($this->customer_id);

        if ($customer && $companyCurrency) {
            if ((string)$customer->currency_id !== $companyCurrency) {
                $rules['exchange_rate'] = [
                    'required',
                ];
            };
        }

        return  $rules;
    }

    public function getRecurringInvoicePayload()
    {
        $company_currency = CompanySetting::getSetting('currency', $this->header('company'));
        $current_currency = $this->currency_id;
        $exchange_rate = $company_currency != $current_currency ? $this->exchange_rate : 1;
        $currency = Customer::find($this->customer_id)->currency_id;

        $nextInvoiceAt = RecurringInvoice::getNextInvoiceDate($this->frequency, $this->starts_at);

        return collect($this->except('items', 'taxes'))
            ->merge([
                'creator_id' => $this->user()->id,
                'company_id' => $this->header('company'),
                'next_invoice_at' => $nextInvoiceAt,
                'tax_per_item' => CompanySetting::getSetting('tax_per_item', $this->header('company')) ?? 'NO ',
                'discount_per_item' => CompanySetting::getSetting('discount_per_item', $this->header('company')) ?? 'NO',
                'due_amount' => $this->total,
                'exchange_rate' => $exchange_rate,
                'currency_id' => $currency
            ])
            ->toArray();
    }
}
