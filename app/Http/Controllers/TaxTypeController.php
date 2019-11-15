<?php
namespace Crater\Http\Controllers;

use Crater\TaxType;
use Crater\User;
use Crater\Http\Requests\TaxTypeRequest;
use Illuminate\Http\Request;

class TaxTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $taxTypes = TaxType::whereCompany($request->header('company'))
            ->latest()
            ->get();

        return response()->json([
            'taxTypes' => $taxTypes
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
    public function store(TaxTypeRequest $request)
    {
        $taxType = new TaxType();
        $taxType->name = $request->name;
        $taxType->percent = $request->percent;
        $taxType->description = $request->description;
        if ($request->has('compound_tax')) {
            $taxType->compound_tax = $request->compound_tax;
        }
        $taxType->company_id = $request->header('company');
        $taxType->save();

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxType $taxType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxType $taxType)
    {
        return response()->json([
            'taxType' => $taxType
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function update(TaxTypeRequest $request, TaxType $taxType)
    {
        $taxType->name = $request->name;
        $taxType->percent = $request->percent;
        $taxType->description = $request->description;
        if ($request->has('collective_tax')) {
            $taxType->collective_tax = $request->collective_tax;
        }
        $taxType->compound_tax = $request->compound_tax;
        $taxType->save();

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxType $taxType)
    {
        if ($taxType->taxes() && $taxType->taxes()->count() > 0) {
            return response()->json([
                'success' => false
            ]);
        }
        $taxType->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
