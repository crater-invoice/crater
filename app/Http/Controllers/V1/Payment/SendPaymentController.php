<?php

namespace Crater\Http\Controllers\V1\Payment;

use Crater\Http\Controllers\Controller;
use Crater\Models\Payment;
use Crater\Http\Requests\SendPaymentRequest;

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
        $response = $payment->send($request->all());

        return response()->json($response);
    }
}
