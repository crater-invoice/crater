<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Illuminate\Http\Request;

class NextNumberController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Invoice $invoice, Estimate $estimate, Payment $payment)
    {
        $key = $request->key;
        $nextNumber = null;

        switch ($key) {
            case 'invoice':
                $nextNumber = $invoice->getNextInvoiceNumber();
                break;
            case 'estimate':
                $nextNumber = $estimate->getNextEstimateNumber();
                break;
            case 'payment':
                $nextNumber = $payment->getNextPaymentNumber();
                break;
            default:
                return;
        }

        return response()->json([
            'nextNumber' => $nextNumber,
        ]);
    }
}
