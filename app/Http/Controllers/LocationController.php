<?php
namespace Laraspace\Http\Controllers;

use Illuminate\Http\Request;
use Laraspace\Country;
use Laraspace\State;
use Laraspace\City;

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
