<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Crater\Conversation;
use Crater\Group;
use Crater\Http\Requests;
use Crater\Notifications\CustomerAdded;
use Crater\User;
use Illuminate\Support\Facades\Hash;
use Crater\Currency;
use Crater\CompanySetting;
use Crater\Address;
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

        $customers = User::customer()
            ->applyFilters($request->only([
                'search',
                'contact_name',
                'display_name',
                'phone',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('users.*',
                DB::raw('sum(invoices.due_amount) as due_amount')
            )
            ->groupBy('users.id')
            ->leftJoin('invoices', 'users.id', '=', 'invoices.user_id')
            ->paginate($limit);

        $siteData = [
            'customers' => $customers
        ];

        return response()->json($siteData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\CustomerRequest $request)
    {
        $verifyEmail = User::where('email', $request->email)->first();


        $customer = new User();
        $customer->name = $request->name;
        $customer->currency_id = $request->currency_id;
        $customer->company_id = $request->header('company');
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->company_name = $request->company_name;
        $customer->contact_name = $request->contact_name;
        $customer->website = $request->website;
        $customer->enable_portal = $request->enable_portal;
        $customer->role = 'customer';
        $customer->password = Hash::make($request->password);
        $customer->save();

        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                $newAddress = new Address();
                $newAddress->name = $address["name"];
                $newAddress->address_street_1 = $address["address_street_1"];
                $newAddress->address_street_2 = $address["address_street_2"];
                $newAddress->city = $address["city"];
                $newAddress->state = $address["state"];
                $newAddress->country_id = $address["country_id"];
                $newAddress->zip = $address["zip"];
                $newAddress->phone = $address["phone"];
                $newAddress->type = $address["type"];
                $newAddress->user_id = $customer->id;
                $newAddress->save();
                $customer->addresses()->save($newAddress);
            }
        }

        $customer = User::with('billingAddress', 'shippingAddress')->find($customer->id);

        return response()->json([
            'customer' => $customer,
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $customer = User::with([
            'billingAddress',
            'shippingAddress',
            'billingAddress.country',
            'shippingAddress.country',
        ])->find($id);

        return response()->json([
            'customer' => $customer
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $customer = User::with('billingAddress', 'shippingAddress')->findOrFail($id);
        $currency = $customer->currency;
        $currencies = Currency::all();

        return response()->json([
            'customer' => $customer,
            'currencies' => $currencies,
            'currency' => $currency
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Requests\CustomerRequest $request)
    {
        $customer = User::find($id);

        if ($request->email != null) {
            $verifyEmail = User::where('email', $request->email)->first();

            if ($verifyEmail) {
                if ($verifyEmail->id !== $customer->id) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Email already in use'
                    ]);
                }
            }
        }

        if ($request->has('password')) {
            $customer->password = Hash::make($request->password);
        }

        $customer->name = $request->name;
        $customer->currency_id = $request->currency_id;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->company_name = $request->company_name;
        $customer->contact_name = $request->contact_name;
        $customer->website = $request->website;
        $customer->enable_portal = $request->enable_portal;
        $customer->save();

        $customer->addresses()->delete();
        if ($request->addresses) {
            foreach ($request->addresses as $address) {
                $newAddress = $customer->addresses()->firstOrNew(['type' => $address["type"]]);
                $newAddress->name = $address["name"];
                $newAddress->address_street_1 = $address["address_street_1"];
                $newAddress->address_street_2 = $address["address_street_2"];
                $newAddress->city = $address["city"];
                $newAddress->state = $address["state"];
                $newAddress->country_id = $address["country_id"];
                $newAddress->zip = $address["zip"];
                $newAddress->phone = $address["phone"];
                $newAddress->type = $address["type"];
                $newAddress->user_id = $customer->id;
                $newAddress->save();
            }
        }

        $customer = User::with('billingAddress', 'shippingAddress')->find($customer->id);

        return response()->json([
            'customer' => $customer,
            'success' => true
        ]);
    }

    /**
     * Remove the specified Customer along side all his/her resources (ie. Estimates, Invoices, Payments and Addresses)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        User::deleteCustomer($id);

        return response()->json([
            'success' => true
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
        foreach ($request->id as $id) {
            User::deleteCustomer($id);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
