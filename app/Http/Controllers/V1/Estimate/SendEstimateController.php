<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\SendEstimatesRequest;
use Crater\Models\Estimate;

class SendEstimateController extends Controller
{
    /**
    * Handle the incoming request.
    *
    * @param  \Crater\Http\Requests\SendEstimatesRequest  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function __invoke(SendEstimatesRequest $request, Estimate $estimate)
    {
        $response = $estimate->send($request->all());

        return response()->json($response);
    }
}
