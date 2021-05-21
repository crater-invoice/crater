<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $customers = User::with('creator')
            ->customer()
            ->applyFilters($request->only([
                'search',
                'contact_name',
                'display_name',
                'phone',
                'customer_id',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->select(
                'users.*',
                DB::raw('sum(invoices.due_amount) as due_amount')
            )
            ->groupBy('users.id')
            ->leftJoin('invoices', 'users.id', '=', 'invoices.user_id')
            ->paginateData($limit);

        return response()->json([
            'customers' => $customers,
            'customerTotalCount' => User::whereRole('customer')->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\CustomerRequest $request)
    {
        $customer = User::createCustomer($request);

        return response()->json([
            'customer' => $customer,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  User $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $customer)
    {
        $customer->load([
            'billingAddress.country',
            'shippingAddress.country',
            'fields.customField',
            'creator',
        ]);

        $currency = $customer->currency;

        return response()->json([
            'customer' => $customer,
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\User $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\CustomerRequest $request, User $customer)
    {
        $customer = User::updateCustomer($request, $customer);

        $customer = User::with('billingAddress', 'shippingAddress', 'fields')->find($customer->id);

        return response()->json([
            'customer' => $customer,
            'success' => true,
        ]);
    }

    /**
     * Remove a list of Customers along side all their resources (ie. Estimates, Invoices, Payments and Addresses)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        User::deleteCustomers($request->ids);

        return response()->json([
            'success' => true,
        ]);
    }
}
