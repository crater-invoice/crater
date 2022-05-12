<?php

namespace Crater\Http\Controllers\V1\Customer\General;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\Customer\CustomerProfileRequest;
use Crater\Http\Resources\Customer\CustomerResource;
use Crater\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function updateProfile(Company $company, CustomerProfileRequest $request)
    {
        $customer = Auth::guard('customer')->user();

        $customer->update($request->validated());

        if (isset($request->is_customer_avatar_removed) && (bool) $request->is_customer_avatar_removed) {
            $customer->clearMediaCollection('customer_avatar');
        }
        if ($customer && $request->hasFile('customer_avatar')) {
            $customer->clearMediaCollection('customer_avatar');

            $customer->addMediaFromRequest('customer_avatar')
            ->toMediaCollection('customer_avatar');
        }

        if ($request->billing !== null) {
            $customer->shippingAddress()->delete();
            $customer->addresses()->create($request->getShippingAddress());
        }

        if ($request->shipping !== null) {
            $customer->billingAddress()->delete();
            $customer->addresses()->create($request->getBillingAddress());
        }

        return new CustomerResource($customer);
    }

    public function getUser(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        return new CustomerResource($customer);
    }
}
