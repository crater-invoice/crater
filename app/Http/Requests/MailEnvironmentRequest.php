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
                    'mail_driver'           => 'required|string|max:50',
                    'mail_host'             => 'required|string|max:50',
                    'mail_port'             => 'required|max:50',
                    'mail_username'         => 'required|string|max:50',
                    'mail_password'         => 'required|string|max:50',
                    'mail_encryption'       => 'required|string|max:50',
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'mailgun':
                return [
                    'mail_driver'           => 'required|string|max:50',
                    'mail_host'             => 'required|string|max:50',
                    'mail_port'             => 'required|max:50',
                    'mail_mailgun_domain'   => 'required|string|max:50',
                    'mail_mailgun_secret'   => 'required|string|max:50',
                    'mail_mailgun_endpoint' => 'required|string|max:50',
                    'mail_encryption'       => 'required|string|max:50',
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'sparkpost':
                return [
                    'mail_driver'           => 'required|string|max:50',
                    'mail_host'             => 'required|string|max:50',
                    'mail_port'             => 'required|max:50',
                    'mail_sparkpost_secret' => 'required|string|max:50',
                    'mail_encryption'       => 'required|string|max:50',
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'ses':
                return [
                    'mail_driver'           => 'required|string|max:50',
                    'mail_host'             => 'required|string|max:50',
                    'mail_port'             => 'required|max:50',
                    'mail_ses_key'          => 'required|string|max:50',
                    'mail_ses_secret'       => 'required|string|max:50',
                    'mail_encryption'       => 'required|string|max:50',
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'mail':
                return [
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'sendmail':
                return [
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;

            case 'mandrill':
                return [
                    'mail_driver'           => 'required|string|max:50',
                    'mail_host'             => 'required|string|max:50',
                    'mail_port'             => 'required|max:50',
                    'mail_mandrill_secret'  => 'required|string|max:50',
                    'mail_encryption'       => 'required|string|max:50',
                    'from_name'             => 'required|string|max:50',
                    'from_mail'             => 'required|string|max:50',
                ];
                break;
        }

    }
}
