<?php

namespace InvoiceShelf\Http\Controllers\V1\Customer\Estimate;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\Customer\EstimateResource;
use InvoiceShelf\Models\Company;
use InvoiceShelf\Models\Estimate;

class EstimatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $estimates = Estimate::with([
            'items',
            'customer',
            'taxes',
            'creator',
        ])
            ->where('status', '<>', 'DRAFT')
            ->whereCustomer(Auth::guard('customer')->id())
            ->applyFilters($request->only([
                'status',
                'estimate_number',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy',
            ]))
            ->latest()
            ->paginateData($limit);

        return EstimateResource::collection($estimates)
            ->additional(['meta' => [
                'estimateTotalCount' => Estimate::where('status', '<>', 'DRAFT')->whereCustomer(Auth::guard('customer')->id())->count(),
            ]]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company, $id)
    {
        $estimate = $company->estimates()
            ->whereCustomer(Auth::guard('customer')->id())
            ->where('id', $id)
            ->first();

        if (! $estimate) {
            return response()->json(['error' => 'estimate_not_found'], 404);
        }

        return new EstimateResource($estimate);
    }
}
