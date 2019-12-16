<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Country;

class LocationController extends Controller
{

    /**
     * Retrive a list of Countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries()
    {
        return response()->json([
            'countries' => Country::all()
        ]);
    }
}
