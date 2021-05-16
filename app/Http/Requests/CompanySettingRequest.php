<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanySettingRequest extends FormRequest
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
            'currency' => [
                'required',
            ],
            'time_zone' => [
                'required',
            ],
            'language' => [
                'required',
            ],
            'fiscal_year' => [
                'required',
            ],
            'moment_date_format' => [
                'required',
            ],
            'carbon_date_format' => [
                'required',
            ],
        ];
    }
}
