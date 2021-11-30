<?php

namespace Crater\Http\Controllers\V1\Admin\Update;

use Crater\Http\Controllers\Controller;
use Crater\Models\Setting;
use Crater\Space\Updater;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function download(Request $request)
    {
        $this->authorize('manage update app');

        $request->validate([
            'version' => 'required',
        ]);

        $path = Updater::download($request->version);

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function unzip(Request $request)
    {
        $this->authorize('manage update app');

        $request->validate([
            'path' => 'required',
        ]);

        try {
            $path = Updater::unzip($request->path);

            return response()->json([
                'success' => true,
                'path' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function copyFiles(Request $request)
    {
        $this->authorize('manage update app');

        $request->validate([
            'path' => 'required',
        ]);

        $path = Updater::copyFiles($request->path);

        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }

    public function migrate(Request $request)
    {
        $this->authorize('manage update app');

        Updater::migrateUpdate();

        return response()->json([
            'success' => true,
        ]);
    }

    public function finishUpdate(Request $request)
    {
        $this->authorize('manage update app');

        $request->validate([
            'installed' => 'required',
            'version' => 'required',
        ]);

        $json = Updater::finishUpdate($request->installed, $request->version);

        return response()->json($json);
    }

    public function checkLatestVersion(Request $request)
    {
        $this->authorize('manage update app');

        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate(Setting::getSetting('version'));

        return response()->json($json);
    }
}
