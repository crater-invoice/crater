<?php

namespace Crater\Http\Requests\Customer;

use Crater\Models\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerProfileRequest extends FormRequest
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
        return [
            'name' => [
                'nullable',
            ],
            'password' => [
                'nullable',
                'min:8',
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('customers')->where('company_id', $this->header('company'))->ignore(Auth::id(), 'id'),
            ],
            'billing.name' => [
                'nullable',
            ],
            'billing.address_street_1' => [
                'nullable',
            ],
            'billing.address_street_2' => [
                'nullable',
            ],
            'billing.city' => [
                'nullable',
            ],
            'billing.state' => [
                'nullable',
            ],
            'billing.country_id' => [
                'nullable',
            ],
            'billing.zip' => [
                'nullable',
            ],
            'billing.phone' => [
                'nullable',
            ],
            'billing.fax' => [
                'nullable',
            ],
            'shipping.name' => [
                'nullable',
            ],
            'shipping.address_street_1' => [
                'nullable',
            ],
            'shipping.address_street_2' => [
                'nullable',
            ],
            'shipping.city' => [
                'nullable',
            ],
            'shipping.state' => [
                'nullable',
            ],
            'shipping.country_id' => [
                'nullable',
            ],
            'shipping.zip' => [
                'nullable',
            ],
            'shipping.phone' => [
                'nullable',
            ],
            'shipping.fax' => [
                'nullable',
            ],
            'customer_avatar' => [
                'nullable',
                'file',
                'mimes:gif,jpg,png',
                'max:20000'
            ]
        ];
    }

    public function getShippingAddress()
    {
        return collect($this->shipping)
            ->merge([
                'type' => Address::SHIPPING_TYPE
            ])
            ->toArray();
    }

    public function getBillingAddress()
    {
        return collect($this->billing)
            ->merge([
                'type' => Address::BILLING_TYPE
            ])
            ->toArray();
    }
}
