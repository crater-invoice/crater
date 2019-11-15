<?php
namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimatesRequest extends FormRequest
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
            'estimate_date' => 'required',
            'expiry_date' => 'required',
            'estimate_number' => 'required|unique:estimates,estimate_number',
            'user_id' => 'required',
            'discount' => 'required|digits_between:1,20',
            'discount_val' => 'required|digits_between:1,20',
            'sub_total' => 'required|digits_between:1,20',
            'total' => 'required|digits_between:1,20',
            'tax' => 'required|digits_between:1,20',
            'estimate_template_id' => 'required',
            'items' => 'required|array',
            'items.*.description' => 'max:255',
            'items.*' => 'required|max:255',
            'items.*.name' => 'required',
            'items.*.quantity' => 'required|digits_between:1,20',
            'items.*.price' => 'required|digits_between:1,20',
            'items.*.discount' => 'digits_between:1,20',
            'items.*.discount_val' => 'digits_between:1,20',
            'items.*.tax' => 'digits_between:1,20',
            'items.*.total' => 'digits_between:1,20',
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['estimate_number'] = $rules['estimate_number'].','.$this->get('id');
        }

        return $rules;
    }
}
