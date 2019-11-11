<?php
namespace Laraspace\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laraspace\User;

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
                    'password' => 'required',
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
                    'email' => 'required|email'
                ];
                break;
            default:
                break;
        }
    }
}
