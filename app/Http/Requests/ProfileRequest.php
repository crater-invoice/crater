<?php
namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Crater\User;

class ProfileRequest extends FormRequest
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
        $user = User::find(1);

        switch ($this->getMethod()) {
            case 'POST':
                return [
                    'name' => 'required',
                    'password' => 'required|min:8',
                    'address_street_1' => 'max:255',
                    'address_street_2' => 'max:255',
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users')->ignore($user->id, 'id')
                    ]
                ];
                break;
            case 'PUT':
                return [
                    'name' => 'required',
                    'address_street_1' => 'max:255',
                    'address_street_2' => 'max:255',
                    'email' => 'required|email'
                ];
                break;
            default:
                break;
        }
    }
}
