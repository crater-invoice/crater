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
            'user_id' => 'required',
            'discount' => 'required',
            'discount_val' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
            'tax' => 'required',
            'estimate_template_id' => 'required',
            'items' => 'required|array',
            'items.*.description' => 'max:255',
            'items.*' => 'required|max:255',
            'items.*.name' => 'required',
            'items.*.quantity' => 'required',
            'items.*.price' => 'required'
        ];

        return $rules;
    }
}
