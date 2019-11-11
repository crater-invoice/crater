<?php
namespace Laraspace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoicesRequest extends FormRequest
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
     * Get the validation rules that apply to the request.s
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'invoice_date' => 'required',
            'due_date' => 'required',
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'user_id' => 'required',
            'discount' => 'required',
            'invoice_template_id' => 'required',
            'items' => 'required|array',
            'items.*' => 'required|max:255',
            'items.*.name' => 'required',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['invoice_number'] = $rules['invoice_number'].','.$this->get('id');
        }

        return $rules;
    }
}
