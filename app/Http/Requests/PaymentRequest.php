<?php
namespace Crater\Http\Requests;

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
            'payment_date' => 'required',
            'payment_number' => 'required|unique:payments,payment_number',
            'user_id' => 'required',
            'amount' => 'required',
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['payment_number'] = $rules['payment_number'].','.$this->route('payment');
        }

        return $rules;
    }
}
