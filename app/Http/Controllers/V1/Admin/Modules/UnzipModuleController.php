<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\UnzipUpdateRequest;
use InvoiceShelf\Space\ModuleInstaller;

class UnzipModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UnzipUpdateRequest $request)
    {
        $this->authorize('manage modules');

        $path = ModuleInstaller::unzip($request->module, $request->path);

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }
}
