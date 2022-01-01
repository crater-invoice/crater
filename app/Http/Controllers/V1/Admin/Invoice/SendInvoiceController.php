<?php

namespace Crater\Http\Controllers\V1\Admin\Invoice;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\SendInvoiceRequest;
use Crater\Models\Invoice;

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
