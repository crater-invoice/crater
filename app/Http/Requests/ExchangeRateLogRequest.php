<?php

namespace Crater\Http\Requests;

use Crater\Models\CompanySetting;
use Illuminate\Foundation\Http\FormRequest;

class ExchangeRateLogRequest extends FormRequest
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
            'exchange_rate' => [
                'required',
            ],
            'currency_id' => [
                'required'
            ]
        ];
    }

    public function getExchangeRateLogPayload()
    {
        $companyCurrency = CompanySetting::getSetting(
            'currency',
            $this->header('company')
        );

        if ($this->currency_id !== $companyCurrency) {
            return collect($this->validated())
                ->merge([
                    'company_id' => $this->header('company'),
                    'base_currency_id' => $companyCurrency,
                ])
                ->toArray();
        }
    }
}
