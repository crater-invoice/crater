<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Invoice;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\SendInvoiceRequest;
use InvoiceShelf\Models\Invoice;

class SendInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('send invoice', $invoice);

        $invoice->send($request->all());

        return response()->json([
            'success' => true,
        ]);
    }
}
