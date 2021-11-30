<?php

namespace Crater\Http\Controllers\V1\Admin\Role;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\RoleRequest;
use Crater\Http\Resources\RoleResource;
use Crater\Models\User;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::when($request->has('orderByField'), function ($query) use ($request) {
            return $query->orderBy($request['orderByField'], $request['orderBy']);
        })
            ->when($request->company_id, function ($query) use ($request) {
                return $query->where('scope', $request->company_id);
            })
            ->get();

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $role = Role::create($request->getRolePayload());

        $this->syncAbilities($request, $role);

        return new RoleResource($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return new RoleResource($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $role->update($request->getRolePayload());

        $this->syncAbilities($request, $role);

        return new RoleResource($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Spatie\Permission\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $users = User::whereIs($role->name)->get()->toArray();

        if (! empty($users)) {
            return respondJson('role_attached_to_users', 'Roles Attached to user');
        }

        $role->delete();

        return response()->json([
            'success' => true
        ]);
    }

    private function syncAbilities(RoleRequest $request, $role)
    {
        foreach (config('abilities.abilities') as $ability) {
            $check = array_search($ability['ability'], array_column($request->abilities, 'ability'));
            if ($check !== false) {
                BouncerFacade::allow($role)->to($ability['ability'], $ability['model']);
            } else {
                BouncerFacade::disallow($role)->to($ability['ability'], $ability['model']);
            }
        }

        return true;
    }
}
