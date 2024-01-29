<?php

namespace InvoiceShelf\Http\Controllers\V1\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use InvoiceShelf\Http\Controllers\Controller;

class CronJobController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Artisan::call('schedule:run');

        return response()->json(['success' => true]);
    }
}
