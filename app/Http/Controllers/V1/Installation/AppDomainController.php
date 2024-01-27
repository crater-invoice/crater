<?php

namespace InvoiceShelf\Http\Controllers\V1\Installation;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\DomainEnvironmentRequest;
use InvoiceShelf\Space\EnvironmentManager;
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
            'success' => true,
        ]);
    }
}
