<?php

namespace Crater\Http\Controllers\V1\Admin\Modules;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UploadModuleRequest;
use Crater\Space\ModuleInstaller;

class UploadModuleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Crater\Http\Requests\UploadModuleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UploadModuleRequest $request)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::upload($request);

        return response()->json($response);
    }
}
