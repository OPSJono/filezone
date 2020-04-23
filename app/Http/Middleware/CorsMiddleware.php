<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsMiddleware {

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        /**
         * @var $response Response
         */
        if($response instanceof Response) {
            $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE', true);
            $response->header('Access-Control-Allow-Headers', $request->header('Access-Control-Request-Headers'), true);
            $response->header('Access-Control-Allow-Origin', '*', true);
        }

        return $response;
    }

}
