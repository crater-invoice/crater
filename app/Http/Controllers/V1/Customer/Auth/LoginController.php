<?php

namespace Crater\Http\Controllers\V1\Customer\Auth;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\Customer\CustomerLoginRequest;
use Crater\Models\Company;
use Crater\Models\Customer;
use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Crater\Http\Requests\Customer\CustomerLoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CustomerLoginRequest $request, Company $company)
    {
        $user = Customer::where('email', $request->email)
            ->where('company_id', $company->id)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->enable_portal) {
            throw ValidationException::withMessages([
                'email' => ['Customer portal not available for this user.'],
            ]);
        }

        Auth::guard('customer')->login($user);

        return response()->json([
            'success' => true
        ]);
    }
}
