<?php

namespace Crater\Http\Requests;

use Crater\Models\Estimate;
use Crater\Rules\UniqueNumber;
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
            'estimate_date' => [
                'required',
            ],
            'expiry_date' => [
                'required',
            ],
            'user_id' => [
                'required',
            ],
            'estimate_number' => [
                'required',
                new UniqueNumber(Estimate::class),
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
            'template_name' => [
                'required'
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*.description' => [
                'max:255',
            ],
            'items.*' => [
                'required',
                'max:255',
            ],
            'items.*.name' => [
                'required',
            ],
            'items.*.quantity' => [
                'required',
            ],
            'items.*.price' => [
                'required',
            ],
        ];

        if ($this->isMethod('PUT')) {
            $rules['estimate_number'] = [
                'required',
                new UniqueNumber(Estimate::class, $this->route('estimate')->id),
            ];
        }

        return $rules;
    }
}
