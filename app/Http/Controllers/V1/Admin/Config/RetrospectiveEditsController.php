<?php

namespace Crater\Http\Controllers\V1\Admin\Config;

use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RetrospectiveEditsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'retrospective_edits' => config('crater.retrospective_edits'),
        ]);
    }
}
