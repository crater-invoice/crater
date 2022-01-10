<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\TaxTypeRequest;
use Crater\Http\Resources\TaxTypeResource;
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
        $this->authorize('viewAny', TaxType::class);

        $limit = $request->has('limit') ? $request->limit : 5;

        $taxTypes = TaxType::applyFilters($request->all())
            ->where('type', TaxType::TYPE_GENERAL)
            ->whereCompany()
            ->latest()
            ->paginateData($limit);

        return TaxTypeResource::collection($taxTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxTypeRequest $request)
    {
        $this->authorize('create', TaxType::class);

        $taxType = TaxType::create($request->getTaxTypePayload());

        return new TaxTypeResource($taxType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function show(TaxType $taxType)
    {
        $this->authorize('view', $taxType);

        return new TaxTypeResource($taxType);
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
        $this->authorize('update', $taxType);

        $taxType->update($request->getTaxTypePayload());

        return new TaxTypeResource($taxType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\TaxType  $taxType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxType $taxType)
    {
        $this->authorize('delete', $taxType);

        if ($taxType->taxes() && $taxType->taxes()->count() > 0) {
            return respondJson('taxes_attached', 'Taxes Attached.');
        }

        $taxType->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
