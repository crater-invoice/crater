<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PaymentMethodRequest;
use Crater\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 5;

        $paymentMethods = PaymentMethod::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'method_id',
                'search',
            ]))
            ->latest()
            ->paginateData($limit);

        return response()->json([
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodRequest $request)
    {
        $paymentMethod = PaymentMethod::createPaymentMethod($request);

        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($request->validated());

        return response()->json([
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $payments = $paymentMethod->payments;

        if ($payments->count() > 0) {
            return response()->json([
                'error' => 'payments_attached',
            ]);
        }

        $paymentMethod->delete();

        return response()->json([
            'success' => 'Payment method deleted successfully',
        ]);
    }
}
