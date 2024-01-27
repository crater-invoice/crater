<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Company;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\CompanyResource;
use InvoiceShelf\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $company = Company::find($request->header('company'));

        return new CompanyResource($company);
    }
}
