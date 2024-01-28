<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Estimate;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\SendEstimatesRequest;
use InvoiceShelf\Models\Estimate;

class SendEstimateController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \InvoiceShelf\Http\Requests\SendEstimatesRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function __invoke(SendEstimatesRequest $request, Estimate $estimate)
    {
        $this->authorize('send estimate', $estimate);

        $response = $estimate->send($request->all());

        return response()->json($response);
    }
}
