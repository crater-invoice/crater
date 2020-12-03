<?php

namespace Crater\Http\Controllers\V1\Auth;

use Crater\Http\Controllers\Controller;
use Crater\Models\User;
use Illuminate\Http\Request;

class IsRegisteredController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if (User::whereEmail($request->email)->first()) {
            return 'true';
        } else {
            return 'false';
        }
    }
}
