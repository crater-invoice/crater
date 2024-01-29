<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Settings;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;

class GetCompanyMailConfigurationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $mailConfig = [
            'from_name' => config('mail.from.name'),
            'from_mail' => config('mail.from.address'),
        ];

        return response()->json($mailConfig);
    }
}
