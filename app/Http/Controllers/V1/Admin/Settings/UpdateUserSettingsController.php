<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Settings;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\UpdateSettingsRequest;

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
        $user = $request->user();

        $user->setSettings($request->settings);

        return response()->json([
            'success' => true,
        ]);
    }
}
