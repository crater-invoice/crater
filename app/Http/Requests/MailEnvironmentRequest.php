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
        switch ($this->get('mail_driver')) {
            case 'smtp':
                return [
                    'mail_driver' => [
                        'required',
                        'string',
                    ],
                    'mail_host' => [
                        'required',
                        'string',
                    ],
                    'mail_port' => [
                        'required',
                    ],
                    'mail_encryption' => [
                        'required',
                        'string',
                    ],
                    'from_name' => [
                        'required',
                        'string',
                    ],
                    'from_mail' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'mailgun':
                return [
                    'mail_driver' => [
                        'required',
                        'string',
                    ],
                    'mail_mailgun_domain' => [
                        'required',
                        'string',
                    ],
                    'mail_mailgun_secret' => [
                        'required',
                        'string',
                    ],
                    'mail_mailgun_endpoint' => [
                        'required',
                        'string',
                    ],
                    'from_name' => [
                        'required',
                        'string',
                    ],
                    'from_mail' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'ses':
                return [
                    'mail_driver' => [
                        'required',
                        'string',
                    ],
                    'mail_host' => [
                        'required',
                        'string',
                    ],
                    'mail_port' => [
                        'required',
                    ],
                    'mail_ses_key' => [
                        'required',
                        'string',
                    ],
                    'mail_ses_secret' => [
                        'required',
                        'string',
                    ],
                    'mail_encryption' => [
                        'nullable',
                        'string',
                    ],
                    'from_name' => [
                        'required',
                        'string',
                    ],
                    'from_mail' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'mail':
                return [
                    'from_name' => [
                        'required',
                        'string',
                    ],
                    'from_mail' => [
                        'required',
                        'string',
                    ],
                ];

                break;

            case 'sendmail':
                return [
                    'from_name' => [
                        'required',
                        'string',
                    ],
                    'from_mail' => [
                        'required',
                        'string',
                    ],
                ];

                break;
        }
    }
}
