<?php

namespace InvoiceShelf\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use InvoiceShelf\Models\Item;
use InvoiceShelf\Rules\RelationNotExist;

class DeleteItemsRequest extends FormRequest
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
            'ids' => [
                'required',
            ],
            'ids.*' => [
                'required',
                Rule::exists('items', 'id'),
                new RelationNotExist(Item::class, 'invoiceItems'),
                new RelationNotExist(Item::class, 'estimateItems'),
                new RelationNotExist(Item::class, 'taxes'),
            ],
        ];
    }
}
