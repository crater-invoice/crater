<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer\Auth;

use Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\Customer\CustomerLoginRequest;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\Customer;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
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
            'success' => true,
        ]);
    }
}
