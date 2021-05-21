<?php

namespace Crater\Http\Controllers\V1\CustomField;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CustomFieldRequest;
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
        $limit = $request->has('limit') ? $request->limit : 5;

        $customFields = CustomField::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'type',
                'search',
            ]))
            ->latest()
            ->paginateData($limit);


        return response()->json([
            'customFields' => $customFields,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CustomFieldRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomFieldRequest $request)
    {
        $customField = CustomField::createCustomField($request);

        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CustomField $customField)
    {
        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
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
        $customField->updateCustomField($request);

        return response()->json([
            'customField' => $customField,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomField $customField)
    {
        if ($customField->customFieldValue()->exists()) {
            return response()->json([
                'error' => 'values_attached',
            ]);
        }

        $customField->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
