<?php

namespace Crater\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CronJobMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('x-authorization-token') && $request->header('x-authorization-token') == config('services.cron_job.auth_token')) {
            return $next($request);
        }

        return response()->json(['unauthorized'], 401);
    }
}
