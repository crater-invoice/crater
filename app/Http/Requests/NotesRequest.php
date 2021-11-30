<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotesRequest extends FormRequest
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
            'type' => [
                'required'
            ],
            'name' => [
                'required',
                Rule::unique('notes')
                    ->where('company_id', $this->header('company'))
                    ->where('type', $this->type)
            ],
            'notes' => [
                'required'
            ],
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = [
                'required',
                Rule::unique('notes')
                    ->ignore($this->route('note')->id)
                    ->where('type', $this->type)
                    ->where('company_id', $this->header('company'))
            ];
        }

        return $rules;
    }

    public function getNotesPayload()
    {
        return collect($this->validated())
            ->merge([
                'company_id' => $this->header('company')
            ])
            ->toArray();
    }
}
