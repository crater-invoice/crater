<?php
namespace Crater\Http\Requests;

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
            'discount' => 'required|digits_between:1,20',
            'discount_val' => 'required|digits_between:1,20',
            'sub_total' => 'required|digits_between:1,20',
            'total' => 'required|digits_between:1,20',
            'tax' => 'required|digits_between:1,20',
            'invoice_template_id' => 'required',
            'items' => 'required|array',
            'items.*' => 'required|max:255',
            'items.*.description' => 'max:255',
            'items.*.name' => 'required',
            'items.*.quantity' => 'required|digits_between:1,20',
            'items.*.price' => 'required|digits_between:1,20',
            'items.*.discount' => 'digits_between:1,20',
            'items.*.discount_val' => 'digits_between:1,20',
            'items.*.tax' => 'digits_between:1,20',
            'items.*.total' => 'digits_between:1,20',
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['invoice_number'] = $rules['invoice_number'].','.$this->get('id');
        }

        return $rules;
    }
}
