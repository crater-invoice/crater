<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer\Estimate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\Customer\EstimateResource;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\Estimate;

class AcceptEstimateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Company $company, $id)
    {
        $estimate = $company->estimates()
            ->whereCustomer(Auth::guard('customer')->id())
            ->where('id', $id)
            ->first();

        if (! $estimate) {
            return response()->json(['error' => 'estimate_not_found'], 404);
        }

        $estimate->update($request->only('status'));

        return new EstimateResource($estimate);
    }
}
