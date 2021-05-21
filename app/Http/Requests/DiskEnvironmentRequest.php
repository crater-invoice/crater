<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiskEnvironmentRequest extends FormRequest
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
        $rules = [];
        switch ($this->get('driver')) {
            case 's3':
                $rules = [
                    'credentials.key' => [
                        'required',
                        'string',
                    ],
                    'credentials.secret' => [
                        'required',
                        'string',
                    ],
                    'credentials.region' => [
                        'required',
                        'string',
                    ],
                    'credentials.bucket' => [
                        'required',
                        'string',
                    ],
                    'credentials.root' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'doSpaces':
                $rules = [
                    'credentials.key' => [
                        'required',
                        'string',
                    ],
                    'credentials.secret' => [
                        'required',
                        'string',
                    ],
                    'credentials.region' => [
                        'required',
                        'string',
                    ],
                    'credentials.bucket' => [
                        'required',
                        'string',
                    ],
                    'credentials.endpoint' => [
                        'required',
                        'string',
                    ],
                    'credentials.root' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'dropbox':
                $rules = [
                    'credentials.token' => [
                        'required',
                        'string',
                    ],
                    'credentials.key' => [
                        'required',
                        'string',
                    ],
                    'credentials.secret' => [
                        'required',
                        'string',
                    ],
                    'credentials.app' => [
                        'required',
                        'string',
                    ],
                    'credentials.root' => [
                        'required',
                        'string',
                    ],
                ];

                break;
        }

        $defaultRules = [
            'name' => [
                'required',
            ],
            'driver' => [
                'required',
            ],
        ];

        return array_merge($rules, $defaultRules);
    }
}
