<?php

// Implementation taken from nova-backup-tool - https://github.com/spatie/nova-backup-tool/

namespace InvoiceShelf\Http\Controllers\V1\Admin\Backup;

use Illuminate\Http\JsonResponse;
use InvoiceShelf\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function respondSuccess(): JsonResponse
    {
        return response()->json([
            'success' => true,
        ]);
    }
}
