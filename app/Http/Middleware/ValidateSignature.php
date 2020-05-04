<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\InvalidSignatureException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateSignature
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
        if ($request->hasValidSignature()) {
            return $next($request);
        }

        try {
            throw new InvalidSignatureException;
        } catch (HttpException $e) {
            return response()->json($e->getMessage())->setStatusCode($e->getStatusCode());
        }
    }
}
