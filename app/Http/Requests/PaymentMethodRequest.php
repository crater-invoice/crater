<?php

namespace InvoiceShelf\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use InvoiceShelf\Models\PaymentMethod;

class PaymentMethodRequest extends FormRequest
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
        $data = [
            'name' => [
                'required',
                Rule::unique('payment_methods')
                    ->where('company_id', $this->header('company')),
            ],
        ];

        if ($this->getMethod() == 'PUT') {
            $data['name'] = [
                'required',
                Rule::unique('payment_methods')
                    ->ignore($this->route('payment_method'), 'id')
                    ->where('company_id', $this->header('company')),
            ];
        }

        return $data;
    }

    public function getPaymentMethodPayload()
    {
        return collect($this->validated())
            ->merge([
                'company_id' => $this->header('company'),
                'type' => PaymentMethod::TYPE_GENERAL,
            ])
            ->toArray();
    }
}
