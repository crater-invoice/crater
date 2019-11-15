<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailEnvironmentRequest extends FormRequest
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
            'mail_driver'           => 'required|string|max:50',
            'mail_host'             => 'required|string|max:50',
            'mail_port'             => 'required|max:50',
            'mail_username'         => 'required|string|max:50',
            'mail_password'         => 'required|string|max:50',
            'mail_encryption'       => 'required|string|max:50',
        ];
    }
}
