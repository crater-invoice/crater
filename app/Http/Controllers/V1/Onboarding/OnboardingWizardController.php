<?php

namespace Crater\Http\Controllers\V1\Onboarding;

use Crater\Http\Controllers\Controller;
use Crater\Models\Setting;
use Illuminate\Http\Request;

class OnboardingWizardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStep(Request $request)
    {
        if (! \Storage::disk('local')->has('database_created')) {
            return response()->json([
                'profile_complete' => 0,
            ]);
        }

        return response()->json([
            'profile_complete' => Setting::getSetting('profile_complete'),
        ]);
    }

    public function updateStep(Request $request)
    {
        $setting = Setting::getSetting('profile_complete');

        if ($setting === 'COMPLETED') {
            return response()->json([
                'profile_complete' => $setting,
            ]);
        }

        Setting::setSetting('profile_complete', $request->profile_complete);

        return response()->json([
            'profile_complete' => Setting::getSetting('profile_complete'),
        ]);
    }
}
