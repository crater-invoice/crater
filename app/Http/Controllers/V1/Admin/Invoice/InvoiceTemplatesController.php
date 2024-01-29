<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Invoice;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Invoice;

class InvoiceTemplatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $invoiceTemplates = Invoice::invoiceTemplates();

        return response()->json([
            'invoiceTemplates' => $invoiceTemplates,
        ]);
    }
}
