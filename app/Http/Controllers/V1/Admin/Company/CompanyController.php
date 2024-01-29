<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Company;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\CompanyResource;
use InvoiceShelf\Models\Company;

class CompanyController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $company = Company::find($request->header('company'));

        return new CompanyResource($company);
    }
}
