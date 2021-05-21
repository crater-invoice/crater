<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Crater\Http\Controllers\Controller;
use Crater\Models\Estimate;

class EstimatePdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Estimate $estimate)
    {
        return $estimate->getGeneratedPDFOrStream('estimate');
    }
}
