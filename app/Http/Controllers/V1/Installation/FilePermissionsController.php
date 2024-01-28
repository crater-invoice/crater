<?php

namespace InvoiceShelf\Http\Controllers\V1\Installation;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\FilePermissionChecker;
use Illuminate\Http\JsonResponse;

class FilePermissionsController extends Controller
{
    /**
     * @var PermissionsChecker
     */
    protected $permissions;

    /**
     * @param PermissionsChecker $checker
     */
    public function __construct(FilePermissionChecker $checker)
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
            'permissions' => $permissions,
        ]);
    }
}
