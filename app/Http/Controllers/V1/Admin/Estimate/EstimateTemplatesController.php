<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Estimate;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Estimate;
use Illuminate\Http\Request;

class EstimateTemplatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', Estimate::class);

        $estimateTemplates = Estimate::estimateTemplates();

        return response()->json([
            'estimateTemplates' => $estimateTemplates
        ]);
    }
}
