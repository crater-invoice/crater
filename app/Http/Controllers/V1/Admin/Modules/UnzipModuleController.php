<?php

namespace Crater\Http\Controllers\V1\Admin\Modules;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UnzipUpdateRequest;
use Crater\Space\ModuleInstaller;

class UnzipModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Crater\Http\Requests\UnzipUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UnzipUpdateRequest $request)
    {
        $this->authorize('manage modules');

        $path = ModuleInstaller::unzip($request->module, $request->path);

        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }
}
