<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\UploadModuleRequest;
use InvoiceShelf\Space\ModuleInstaller;

class UploadModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \InvoiceShelf\Http\Requests\UploadModuleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UploadModuleRequest $request)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::upload($request);

        return response()->json($response);
    }
}
