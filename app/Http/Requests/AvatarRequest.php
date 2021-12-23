<?php

namespace Crater\Http\Requests;

use Crater\Rules\Base64Mime;
use Illuminate\Foundation\Http\FormRequest;

class AvatarRequest extends FormRequest
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
            'admin_avatar' => [
                'nullable',
                'file',
                'mimes:gif,jpg,png',
                'max:20000'
            ],
            'avatar' => [
                'nullable',
                new Base64Mime(['gif', 'jpg', 'png'])
            ]
        ];
    }
}
