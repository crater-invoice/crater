<?php

namespace Crater\Http\Middleware;

use Closure;
use Crater\Models\CompanySetting;
use Crater\Models\FileDisk;

class ConfigMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Storage::disk('local')->has('database_created')) {
            $setting = CompanySetting::getSetting('time_zone', $request->header('company'));

            $timezone = config('app.timezone');

            if ($setting && $setting != null && $setting != $timezone) {
                config(['app.timezone' => $setting]);
                date_default_timezone_set($setting);
            }

            if ($request->has('file_disk_id')) {
                $file_disk = FileDisk::find($request->file_disk_id);
            } else {
                $file_disk = FileDisk::whereSetAsDefault(true)->first();
            }

            if ($file_disk) {
                $file_disk->setConfig();
            }
        }

        return $next($request);
    }
}
