<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\ModuleInstaller;

class CopyModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::copyFiles($request->module, $request->path);

        return response()->json([
            'success' => $response,
        ]);
    }
}
