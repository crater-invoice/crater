<?php

namespace InvoiceShelf\Http\Controllers\V1\Installation;

use Illuminate\Http\JsonResponse;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\RequirementsChecker;

class RequirementsController extends Controller
{
    /**
     * @var RequirementsChecker
     */
    protected $requirements;

    public function __construct(RequirementsChecker $checker)
    {
        $this->requirements = $checker;
    }

    /**
     * Display the requirements page.
     *
     * @return JsonResponse
     */
    public function requirements()
    {
        $phpSupportInfo = $this->requirements->checkPHPVersion(
            config('installer.core.minPhpVersion')
        );

        $requirements = $this->requirements->check(
            config('installer.requirements')
        );

        return response()->json([
            'phpSupportInfo' => $phpSupportInfo,
            'requirements' => $requirements,
        ]);
    }
}
