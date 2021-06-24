<?php

namespace Crater\Http\Controllers\V1\Onboarding;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DomainEnvironmentRequest;
use Crater\Space\EnvironmentManager;
use Illuminate\Support\Facades\Artisan;

class AppDomainController extends Controller
{
    /**
     *
     * @param DomainEnvironmentRequest $request
     */
    public function __invoke(DomainEnvironmentRequest $request)
    {
        Artisan::call('optimize:clear');

        $environmentManager = new EnvironmentManager();

        $results = $environmentManager->saveDomainVariables($request);

        if (in_array('error', $results)) {
            return response()->json($results);
        }

        return response()->json([
            'success' => false,
        ]);
    }
}
