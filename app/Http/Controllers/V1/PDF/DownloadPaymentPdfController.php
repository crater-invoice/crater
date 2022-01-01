<?php

namespace Crater\Http\Controllers\V1\PDF;

use Crater\Http\Controllers\Controller;
use Crater\Models\Payment;

class DownloadPaymentPdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Payment $payment)
    {
        $path = storage_path('app/temp/payment/'.$payment->id.'.pdf');

        return response()->download($path);
    }
}
