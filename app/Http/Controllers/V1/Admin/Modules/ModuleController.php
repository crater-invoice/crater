<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\ModuleResource;
use InvoiceShelf\Space\ModuleInstaller;

class ModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $module)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::getModule($module);

        if (! $response->success) {
            return response()->json($response);
        }

        return (new ModuleResource($response->module))
            ->additional(['meta' => [
                'modules' => ModuleResource::collection(collect($response->modules)),
            ]]);
    }
}
