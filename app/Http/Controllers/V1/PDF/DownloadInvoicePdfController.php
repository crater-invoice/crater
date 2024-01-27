<?php

namespace InvoiceShelf\Http\Controllers\V1\PDF;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Invoice;

class DownloadInvoicePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Invoice $invoice)
    {
        $path = storage_path('app/temp/invoice/'.$invoice->id.'.pdf');

        return response()->download($path);
    }
}
