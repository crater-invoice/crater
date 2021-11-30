<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseCategoryRequest extends FormRequest
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
            'name' => [
                'required',
            ],
            'description' => [
                'nullable',
            ],
        ];
    }

    public function getExpenseCategoryPayload()
    {
        return collect($this->validated())
            ->merge([
                'company_id' => $this->header('company')
            ])
            ->toArray();
    }
}
