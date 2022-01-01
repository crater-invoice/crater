<?php

namespace Crater\Http\Controllers\V1\Admin\RecurringInvoice;

use Crater\Http\Controllers\Controller;
use Crater\Models\RecurringInvoice;
use Illuminate\Http\Request;

class RecurringInvoiceFrequencyController extends Controller
{
    public function __invoke(Request $request)
    {
        $nextInvoiceAt = RecurringInvoice::getNextInvoiceDate($request->frequency, $request->starts_at);

        return response()->json([
            'success' => true,
            'next_invoice_at' => $nextInvoiceAt,
        ]);
    }
}
