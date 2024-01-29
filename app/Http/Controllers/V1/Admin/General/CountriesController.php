<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\CountryResource;
use InvoiceShelf\Models\Country;

class CountriesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $countries = Country::all();

        return CountryResource::collection($countries);
    }
}
