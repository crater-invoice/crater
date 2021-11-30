<?php

namespace Crater\Http\Controllers\V1\Admin\CustomField;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomFieldRequest;
use Crater\Http\Resources\CustomFieldResource;
use Crater\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CustomField::class);

        $limit = $request->has('limit') ? $request->limit : 5;

        $customFields = CustomField::applyFilters($request->all())
            ->whereCompany()
            ->latest()
            ->paginateData($limit);

        return CustomFieldResource::collection($customFields);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CustomFieldRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomFieldRequest $request)
    {
        $this->authorize('create', CustomField::class);

        $customField = CustomField::createCustomField($request);

        return new CustomFieldResource($customField);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        $this->authorize('view', $customField);

        return new CustomFieldResource($customField);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomFieldRequest $request, CustomField $customField)
    {
        $this->authorize('update', $customField);

        $customField->updateCustomField($request);

        return new CustomFieldResource($customField);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomField $customField)
    {
        $this->authorize('delete', $customField);

        if ($customField->customFieldValues()->exists()) {
            $customField->customFieldValues()->delete();
        }

        $customField->forceDelete();

        return response()->json([
            'success' => true,
        ]);
    }
}
