<?php

namespace Crater\Http\Controllers\V1\Admin\Company;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\CompanyResource;
use Crater\Models\Company;
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
