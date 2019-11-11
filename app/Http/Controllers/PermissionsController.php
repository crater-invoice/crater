<?php

namespace Laraspace\Http\Controllers;

use Laraspace\Space\PermissionsChecker;
use Illuminate\Http\JsonResponse;

class PermissionsController extends Controller
{
    /**
     * @var PermissionsChecker
     */
    protected $permissions;

    /**
     * @param PermissionsChecker $checker
     */
    public function __construct(PermissionsChecker $checker)
    {
        $this->permissions = $checker;
    }

    /**
     * Display the permissions check page.
     *
     * @return JsonResponse
     */
    public function permissions()
    {
        $permissions = $this->permissions->check(
            config('installer.permissions')
        );

        return response()->json([
            'permissions' => $permissions
        ]);
    }
}
