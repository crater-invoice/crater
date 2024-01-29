<?php

namespace InvoiceShelf\Http\Controllers\V1\PDF;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Estimate;

class EstimatePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Estimate $estimate)
    {
        if ($request->has('preview')) {
            return $estimate->getPDFData();
        }

        return $estimate->getGeneratedPDFOrStream('estimate');
    }
}
