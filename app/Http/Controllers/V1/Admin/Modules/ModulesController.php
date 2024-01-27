<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Modules;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\ModuleInstaller;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('manage modules');

        $response = ModuleInstaller::getModules();

        return $response;
    }
}
