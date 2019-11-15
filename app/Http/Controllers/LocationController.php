<?php
namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Country;
use Crater\State;
use Crater\City;

class LocationController extends Controller
{
    public function getCountries()
    {
        return response()->json([
            'countries' => Country::all()
        ]);
    }

    public function getStates($id)
    {
        return response()->json([
            'states' => Country::find($id)->states
        ]);
    }

    public function getCities($id)
    {
        return response()->json([
            'cities' => State::find($id)->cities
        ]);
    }
}
