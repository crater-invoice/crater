<?php
namespace Crater\Http\Middleware;

use Closure;
use Exception;
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
            try{
                $setting = CompanySetting::getSetting('time_zone', $request->header('company'));
            }catch (Exception $exception){
                $setting = null;
            }

            $timezone = config('app.timezone');

            if ($setting && $setting != $timezone) {
                config(['app.timezone' => $setting]);
            }

            if($request->has('file_disk_id')) {
                $file_disk = FileDisk::find($request->file_disk_id);
            } else {
                try{
                    $file_disk = FileDisk::whereSetAsDefault(true)->first();
                }catch (Exception $exception){
                    $file_disk = null;
                }
            }

            if($file_disk) {
                $file_disk->setConfig();
            }
        }

        return $next($request);
    }
}
