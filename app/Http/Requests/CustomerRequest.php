<?php
namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        switch ($this->getMethod()) {
            case 'POST':
                return [
                    'name' => 'required',
                    'addresses.*.address_street_1' => 'max:255',
                    'addresses.*.address_street_2' => 'max:255',
                    'email' => 'email|nullable|unique:users,email',
                ];
                break;
            case 'PUT':
                return [
                    'name' => 'required',
                    'addresses.*.address_street_1' => 'max:255',
                    'addresses.*.address_street_2' => 'max:255',
                ];
                break;
            default:
                break;
        }
    }
}
