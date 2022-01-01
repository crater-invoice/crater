<?php

namespace Crater\Http\Controllers\V1\Admin\Users;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteUserRequest;
use Crater\Http\Requests\UserRequest;
use Crater\Http\Resources\UserResource;
use Crater\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $limit = $request->has('limit') ? $request->limit : 10;

        $user = $request->user();

        $users = User::applyFilters($request->all())
            ->where('id', '<>', $user->id)
            ->latest()
            ->paginate($limit);

        return UserResource::collection($users)
            ->additional(['meta' => [
                'user_total_count' => User::count(),
            ]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::createFromRequest($request);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UserRequest  $request
     * @param  \Crater\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->updateFromRequest($request);

        return new UserResource($user);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteUserRequest $request)
    {
        $this->authorize('delete multiple users', User::class);

        if ($request->users) {
            User::deleteUsers($request->users);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
