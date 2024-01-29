<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Estimate;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Estimate;

class ChangeEstimateStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Estimate $estimate)
    {
        $this->authorize('send estimate', $estimate);

        $estimate->update($request->only('status'));

        return response()->json([
            'success' => true,
        ]);
    }
}
