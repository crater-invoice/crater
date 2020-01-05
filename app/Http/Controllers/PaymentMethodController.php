<?php

namespace Crater\Http\Controllers;

use Crater\PaymentMethod;
use Illuminate\Http\Request;
use Crater\Http\Requests\PaymentMethodRequest;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::whereCompany($request->header('company'))
            ->latest()
            ->get();

        return response()->json([
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodRequest $request)
    {
        $paymentMethod = new PaymentMethod;
        $paymentMethod->name = $request->name;
        $paymentMethod->company_id = $request->header('company');
        $paymentMethod->save();

        return response()->json([
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return response()->json([
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->name = $request->name;
        $paymentMethod->company_id = $request->header('company');
        $paymentMethod->save();

        return response()->json([
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $payments = $paymentMethod->payments;

        if ($payments->count() > 0) {
            return response()->json([
                'error' => 'payments_attached'
            ]);
        }

        $paymentMethod->delete();

        return response()->json([
            'success' => 'Payment method deleted successfully'
        ]);
    }
}
