<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Illuminate\Http\Request;
use Crater\Models\Estimate;
use Crater\Http\Requests\EstimatesRequest;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteEstimatesRequest;
use Crater\Jobs\GenerateEstimatePdfJob;

class EstimatesController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $estimates = Estimate::with([
                'items',
                'user',
                'estimateTemplate',
                'taxes',
                'creator'
            ])
            ->join('users', 'users.id', '=', 'estimates.user_id')
            ->applyFilters($request->only([
                'status',
                'customer_id',
                'estimate_id',
                'estimate_number',
                'from_date',
                'to_date',
                'search',
                'orderByField',
                'orderBy'
            ]))
            ->whereCompany($request->header('company'))
            ->select('estimates.*', 'users.name')
            ->latest()
            ->paginateData($limit);

        $siteData = [
            'estimates' => $estimates,
            'estimateTotalCount' => Estimate::count()
        ];

        return response()->json($siteData);
    }

    public function store(EstimatesRequest $request)
    {
        $estimate = Estimate::createEstimate($request);

        if ($request->has('estimateSend')) {
            $estimate->send($request->title, $request->body);
        }

        GenerateEstimatePdfJob::dispatch($estimate);

        return response()->json([
            'estimate' => $estimate
        ]);
    }

    public function show(Request $request, Estimate $estimate)
    {
        $estimate->load([
            'items',
            'items.taxes',
            'user',
            'estimateTemplate',
            'creator',
            'taxes',
            'taxes.taxType',
            'fields.customField'
        ]);

        return response()->json([
            'estimate' => $estimate,
            'nextEstimateNumber' => $estimate->getEstimateNumAttribute(),
            'estimatePrefix' => $estimate->getEstimatePrefixAttribute(),
        ]);
    }

    public function update(EstimatesRequest $request, Estimate $estimate)
    {
        $estimate = $estimate->updateEstimate($request);

        GenerateEstimatePdfJob::dispatch($estimate, true);

        return response()->json([
            'estimate' => $estimate
        ]);
    }

    public function delete(DeleteEstimatesRequest $request)
    {
        Estimate::destroy($request->ids);

        return response()->json([
            'success' => true
        ]);
    }
}
