<?php

namespace Crater\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MailSenderRequest extends FormRequest
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
                Rule::unique('mail_senders')
                ->where('company_id', $this->header('company'))
            ],
            'driver' => [
                'required',
            ],
            'is_default' => [
                'nullable'
            ],
            'bcc' => [
                'nullable'
            ],
            'cc' => [
                'nullable'
            ],
            'from_address' => [
                'nullable'
            ],
            'from_name' => [
                'nullable'
            ],
            'settings' => [
                'nullable'
            ],
            'settings.*' => [
                'nullable'
            ]
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = [
                'nullable',
                Rule::unique('mail_senders')
                    ->ignore($this->route('mail_sender')->id)
                    ->where('company_id', $this->header('company'))
            ];
        }

        return $rules;
    }

    public function getMailSenderPayload()
    {
        $data = $this->validated();

        if ($data['settings']['encryption'] == 'none') {
            $data['settings']['encryption'] = '';
        }

        return collect($data)
            ->merge([
                'company_id' => $this->header('company'),
            ])
            ->toArray();
    }
}
