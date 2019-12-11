<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatabaseEnvironmentRequest extends FormRequest
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
            'app_url'               => 'required|url',
            'database_connection'   => 'required|string',
            'database_hostname'     => 'required|string',
            'database_port'         => 'required|numeric',
            'database_name'         => 'required|string',
            'database_username'     => 'required|string',
        ];
    }
}
