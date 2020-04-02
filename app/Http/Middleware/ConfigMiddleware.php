<?php
namespace Crater\Http\Middleware;

use Closure;
use Crater\CompanySetting;

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
        if (\Storage::disk('local')->has('installed')) {
            $setting = CompanySetting::getSetting('time_zone', $request->header('company'));
            $timezone = config('app.timezone');
            if ($setting && $setting != null && $setting != $timezone) {
                config(['app.timezone' => $setting]);
            }
        }

        return $next($request);
    }
}
