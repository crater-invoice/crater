<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Item;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests;
use InvoiceShelf\Http\Requests\DeleteItemsRequest;
use InvoiceShelf\Http\Resources\ItemResource;
use InvoiceShelf\Models\Item;
use InvoiceShelf\Models\TaxType;

class ItemsController extends Controller
{
    /**
     * Retrieve a list of existing Items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Item::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $items = Item::whereCompany()
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->applyFilters($request->all())
            ->select('items.*', 'units.name as unit_name')
            ->latest()
            ->paginateData($limit);

        return ItemResource::collection($items)
            ->additional(['meta' => [
                'tax_types' => TaxType::whereCompany()->latest()->get(),
                'item_total_count' => Item::whereCompany()->count(),
            ]]);
    }

    /**
     * Create Item.
     *
     * @param  InvoiceShelf\Http\Requests\ItemsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\ItemsRequest $request)
    {
        $this->authorize('create', Item::class);

        $item = Item::createItem($request);

        return new ItemResource($item);
    }

    /**
     * get an existing Item.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        return new ItemResource($item);
    }

    /**
     * Update an existing Item.
     *
     * @param  InvoiceShelf\Http\Requests\ItemsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\ItemsRequest $request, Item $item)
    {
        $this->authorize('update', $item);

        $item = $item->updateItem($request);

        return new ItemResource($item);
    }

    /**
     * Delete a list of existing Items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteItemsRequest $request)
    {
        $this->authorize('delete multiple items');

        Item::destroy($request->ids);

        return response()->json([
            'success' => true,
        ]);
    }
}
