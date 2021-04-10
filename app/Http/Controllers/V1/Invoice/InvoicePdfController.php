<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Crater\Http\Controllers\Controller;
use Crater\Models\Invoice;

class InvoicePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Invoice $invoice)
    {
        return $invoice->getGeneratedPDFOrStream('invoice');
    }
}
