<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UpdateSettingsRequest;

class UpdateUserSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateSettingsRequest $request)
    {
        $user = Auth::user();

        $user->setSettings($request->settings);

        return response()->json([
            'success' => true,
        ]);
    }
}
