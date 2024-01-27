<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Payment;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\SendPaymentRequest;
use InvoiceShelf\Models\Payment;

class SendPaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendPaymentRequest $request, Payment $payment)
    {
        $this->authorize('send payment', $payment);

        $response = $payment->send($request->all());

        return response()->json($response);
    }
}
