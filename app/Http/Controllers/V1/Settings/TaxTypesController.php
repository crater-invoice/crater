<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TaxTypeRequest;
use Crater\Models\TaxType;
use Illuminate\Http\Request;

class TaxTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 5;

        $taxTypes = TaxType::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'tax_type_id',
                'search',
                'orderByField',
                'orderBy',
            ]))
            ->latest()
            ->paginateData($limit);

        return response()->json([
            'taxTypes' => $taxTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxTypeRequest $request)
    {
        $data = $request->validated();

        $data['company_id'] = $request->header('company');

        $taxType = TaxType::create($data);

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxType $taxType)
    {
        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function update(TaxTypeRequest $request, TaxType $taxType)
    {
        $taxType->update($request->validated());

        return response()->json([
            'taxType' => $taxType,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxType $taxType)
    {
        if ($taxType->taxes() && $taxType->taxes()->count() > 0) {
            return response()->json([
                'success' => false,
            ]);
        }
        $taxType->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
