<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadModuleRequest extends FormRequest
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
            'avatar' => [
                'required',
                'file',
                'mimes:zip',
                'max:20000'
            ],
            'module' => [
                'required',
                'string',
                'max:100'
            ]
        ];
    }
}
