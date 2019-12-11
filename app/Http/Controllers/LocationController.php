<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Country;

class LocationController extends Controller
{
    public function getCountries()
    {
        return response()->json([
            'countries' => Country::all()
        ]);
    }
}
