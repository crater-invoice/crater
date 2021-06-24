<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $templates = Storage::disk('views')->files('/app/pdf/estimate');
        $estimateTemplates = [];

        foreach ($templates as $key => $template) {
            $templateName = Str::before(basename($template), '.blade.php');
            $estimateTemplates[$key]['name'] = $templateName;
            $estimateTemplates[$key]['path'] = asset('assets/img/PDF/'.$templateName.'.png');
        }

        return response()->json([
            'templates' => $estimateTemplates
        ]);
    }
}
