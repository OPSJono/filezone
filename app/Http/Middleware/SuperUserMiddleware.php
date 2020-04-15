<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->user()->superuser === 1) {
            return $next($request);
        }

        return response()->json(['Unauthorized.'])->setStatusCode(401);
    }
}
