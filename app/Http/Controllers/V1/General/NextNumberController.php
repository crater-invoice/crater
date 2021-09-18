<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Services\SerialNumberFormatter;
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
        $serial = new SerialNumberFormatter();

        switch ($key) {
            case 'invoice':
                $nextNumber = $serial->setModel($invoice)->getNextNumber();
                break;
            case 'estimate':
                $nextNumber = $serial->setModel($estimate)->getNextNumber();
                break;
            case 'payment':
                $nextNumber = $serial->setModel($payment)->getNextNumber();
                break;
            default:
                return;
        }

        return response()->json([
            'nextNumber' => $nextNumber,
        ]);
    }
}
