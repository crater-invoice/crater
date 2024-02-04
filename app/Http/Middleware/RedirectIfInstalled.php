<?php

namespace InvoiceShelf\Http\Middleware;

use Closure;
use InvoiceShelf\Models\Setting;
use InvoiceShelf\Space\InstallUtils;

class RedirectIfInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (InstallUtils::dbMarkerExists()) {
            if (Setting::getSetting('profile_complete') === 'COMPLETED') {
                return redirect('login');
            }
        }

        return $next($request);
    }
}
