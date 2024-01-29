<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\ModuleInstaller;

class ApiTokenController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::checkToken($request->api_token);

        return $response;
    }
}
