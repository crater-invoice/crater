<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceTemplatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $templates = Storage::disk('views')->files('/app/pdf/invoice');
        $invoiceTemplates = [];

        foreach ($templates as $key => $template) {
            $templateName = Str::before(basename($template), '.blade.php');
            $invoiceTemplates[$key]['name'] = $templateName;
            $invoiceTemplates[$key]['path'] = asset('assets/img/PDF/'.$templateName.'.png');
        }

        return response()->json([
            'invoiceTemplates' => $invoiceTemplates,
        ]);
    }
}
