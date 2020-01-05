<?php

namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Setting;
use Crater\Mail\TestMail;
use Mail;

class SettingsController extends Controller
{

    /**
     * Retrive App Version.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppVersion(Request $request)
    {
        $version = Setting::getSetting('version');

        return response()->json([
            'version' => $version,
        ]);
    }

    public function testEmailConfig(Request $request)
    {
        $this->validate($request, [
            'to' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ]);

        Mail::to($request->to)->send(new TestMail($request->subject, $request->message));

        return response()->json([
            'success' => true
        ]);
    }
}
