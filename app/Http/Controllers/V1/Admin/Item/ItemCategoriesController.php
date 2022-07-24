<?php

namespace Crater\Http\Controllers\V1\Admin\Item;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ItemCategoryRequest;
use Crater\Http\Resources\ItemCategoryResource;
use Crater\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ItemCategory::class);

        $limit = $request->has('limit') ? $request->limit : 5;

        $categories = ItemCategory::applyFilters($request->all())
            ->whereCompany()
            ->latest()
            ->paginateData($limit);

        return ItemCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return ItemCategoryResource
     */
    public function store(ItemCategoryRequest $request)
    {
        $this->authorize('create', ItemCategory::class);

        $category = ItemCategory::create($request->getItemCategoryPayload());

        return new ItemCategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ItemCategory $category
     * @return ItemCategoryResource
     */
    public function show(ItemCategory $category)
    {
        $this->authorize('view', $category);

        return new ItemCategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\ItemCategory $ItemCategory
     * @return ItemCategoryResource
     */
    public function update(ItemCategoryRequest $request, ItemCategory $category)
    {
        $this->authorize('update', $category);

        $category->update($request->getItemCategoryPayload());

        return new ItemCategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\ItemCategory $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ItemCategory $category)
    {
        $this->authorize('delete', $category);

        if ($category->items() && $category->items()->count() > 0) {
            return respondJson('item_attached', 'Item Attached');
        }

        $category->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
