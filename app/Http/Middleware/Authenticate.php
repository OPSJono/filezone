<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return response()->json('Unauthorized.')->setStatusCode(401)
                ->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE', true)
                ->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'), true)
                ->header('Access-Control-Allow-Origin', '*', true);
        }

        return $next($request);
    }
}
