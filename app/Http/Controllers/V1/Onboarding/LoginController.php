<?php

namespace Crater\Http\Controllers\V1\Onboarding;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::where('role', 'super admin')->first();
        Auth::login($user);

        return response()->json(['success' => true]);
    }
}
