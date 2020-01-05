<?php

namespace Crater\Http\Controllers;

use Crater\Unit;
use Illuminate\Http\Request;
use Crater\Http\Requests\UnitRequest;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $units = Unit::whereCompany($request->header('company'))
            ->latest()
            ->get();

        return response()->json([
            'units' => $units
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UnitRequest $request)
    {
        $unit = new Unit;
        $unit->name = $request->name;
        $unit->company_id = $request->header('company');
        $unit->save();

        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit)
    {
        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(UnitRequest $request, Unit $unit)
    {
        $unit->name = $request->name;
        $unit->company_id = $request->header('company');
        $unit->save();

        return response()->json([
            'unit' => $unit
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        $items = $unit->items;

        if ($items->count() > 0) {
            return response()->json([
                'error' => 'items_attached'
            ]);
        }

        $unit->delete();

        return response()->json([
            'success' => 'Unit deleted successfully'
        ]);
    }
}
