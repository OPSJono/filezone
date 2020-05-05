<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\InvalidSignatureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LowercaseEmailInputMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return Response|JsonResponse
     *
     * @throws InvalidSignatureException
     */
    public function handle($request, Closure $next)
    {
        if($request->has('email') && is_string($request->input('email'))) {
            $request->merge([
                'email' => htmlspecialchars(strtolower(trim($request->input('email'))))
            ]);
        }

        return $next($request);
    }
}
