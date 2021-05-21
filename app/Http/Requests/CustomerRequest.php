<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
            'name' => [
                'required',
            ],
            'addresses.*.address_street_1' => [
                'max:255',
            ],
            'addresses.*.address_street_2' => [
                'max:255',
            ],
            'email' => [
                'email',
                'nullable',
                'unique:users,email',
            ],
        ];

        if ($this->isMethod('PUT') && $this->email != null) {
            $rules = [
                'email' => [
                    'email',
                    'nullable',
                    Rule::unique('users')->ignore($this->route('customer')->id),
                ],
            ];
        };

        return $rules;
    }
}
