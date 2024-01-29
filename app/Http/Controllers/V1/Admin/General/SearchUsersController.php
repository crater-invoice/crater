<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\User;

class SearchUsersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('create', User::class);

        $users = User::whereEmail($request->email)
            ->latest()
            ->paginate(10);

        return response()->json(['users' => $users]);
    }
}
