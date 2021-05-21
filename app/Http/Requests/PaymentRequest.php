<?php

namespace Crater\Http\Requests;

use Crater\Models\Payment;
use Crater\Rules\UniqueNumber;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        $rules = [
            'payment_date' => [
                'required',
            ],
            'user_id' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'payment_number' => [
                'required',
                new UniqueNumber(Payment::class),
            ],
            'invoice_id' => [
                'nullable',
            ],
            'payment_method_id' => [
                'nullable',
            ],
            'notes' => [
                'nullable',
            ],
        ];

        if ($this->isMethod('PUT')) {
            $rules['payment_number'] = [
                'required',
                new UniqueNumber(Payment::class, $this->route('payment')->id),
            ];
        }

        return $rules;
    }
}
