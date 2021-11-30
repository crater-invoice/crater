<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
                'string',
                Rule::unique('roles')->where('scope', $this->header('company'))
            ],
            'abilities' => [
                'required'
            ],
            'abilities.*' => [
                'required'
            ]
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['name'] = [
                'required',
                'string',
                Rule::unique('roles')
                    ->ignore($this->route('role')->id, 'id')
                    ->where('scope', $this->header('company'))
            ];
        }

        return $rules;
    }

    public function getRolePayload()
    {
        return collect($this->except('abilities'))
            ->merge([
                'scope' => $this->header('company'),
            ])
            ->toArray();
    }
}
