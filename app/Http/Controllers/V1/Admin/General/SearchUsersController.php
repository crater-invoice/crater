<?php

namespace Crater\Http\Controllers\V1\Admin\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\User;
use Illuminate\Http\Request;

class SearchUsersController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
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
