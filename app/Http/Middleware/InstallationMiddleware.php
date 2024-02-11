<?php

namespace InvoiceShelf\Http\Middleware;

use Closure;
use InvoiceShelf\Models\Setting;
use InvoiceShelf\Space\InstallUtils;

class InstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! InstallUtils::isDbCreated() || Setting::getSetting('profile_complete') !== 'COMPLETED') {
            return redirect('/installation');
        }

        return $next($request);
    }
}
