<?php

namespace Crater\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CronJobOrgMiddleware
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
        if ($request->header('Authorization-token')) {
            return $next($request);
        }

        return response()->json(['unauthorized'], 401);
    }
}
