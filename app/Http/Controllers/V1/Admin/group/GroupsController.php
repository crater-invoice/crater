<?php

namespace Crater\Http\Controllers\V1\Admin\Group;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests;
use Crater\Http\Resources\GroupResource;
use Crater\Models\Group;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Retrieve a list of existing Items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Group::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $groups = Group::applyFilters($request->all())
            ->latest()
            ->paginateData($limit);

        return (GroupResource::collection($groups))
            ->additional(['meta' => [
                'group_total_count' => Group::count(),
            ]]);
    }

    /**
     * Create Item.
     *
     * @param  \Crater\Http\Requests\GroupsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\GroupsRequest $request)
    {
        $this->authorize('create', Group::class);

        $group = Group::createGroup($request);

        return new GroupResource($group);
    }

    /**
     * get an existing Group.
     *
     * @param  Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Group $group)
    {
        $this->authorize('view', $group);

        return new GroupResource($group);
    }

    /**
     * Update an existing Item.
     *
     * @param  \Crater\Http\Requests\GroupsRequest $request
     * @param  \Crater\Models\Group $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Requests\GroupsRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $group = $group->updateGroup($request);

        return new GroupResource($group);
    }

    /**
     * Delete a list of existing Items.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Requests\DeleteGroupsRequest $request)
    {
        $this->authorize('delete multiple groups');

        Group::destroy($request->ids);

        return response()->json([
            'success' => true,
        ]);
    }
}
