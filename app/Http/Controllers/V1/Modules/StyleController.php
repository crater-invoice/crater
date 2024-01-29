<?php

namespace InvoiceShelf\Http\Controllers\V1\Modules;

use DateTime;
use Illuminate\Support\Arr;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Services\Module\ModuleFacade;
use Request;

class StyleController extends Controller
{
    /**
     * Serve the requested stylesheet.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(Request $request, string $style)
    {
        $path = Arr::get(ModuleFacade::allStyles(), $style);

        abort_if(is_null($path), 404);

        return response(
            file_get_contents($path),
            200,
            [
                'Content-Type' => 'text/css',
            ]
        )->setLastModified(DateTime::createFromFormat('U', filemtime($path)));
    }
}
