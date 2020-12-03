<?php

namespace Crater\Http\Controllers\V1\Onboarding;

use Crater\Http\Controllers\Controller;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinishController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        \Storage::disk('local')->put('database_created', 'database_created');

        $user = User::where('role', 'super admin')->first();
        Auth::login($user);
    }
}
