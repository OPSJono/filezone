<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use League\OAuth2\Server\Exception\OAuthServerException;
use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as
    ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        OAuthServerException::class, // invalid tokens
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response|JsonResponse
     *
     * @throws Throwable
     *
     * This is the same as the parent method, only I force the JSON response.
     */
    public function render($request, Throwable $exception)
    {

        if (method_exists($exception, 'render')) {
            return $exception->render($request);
        } elseif ($exception instanceof Responsable) {
            return $exception->toResponse($request);
        }

        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        } elseif ($exception instanceof ModelNotFoundException) {
            $exception = new NotFoundHttpException($exception->getMessage(), $exception);
        } elseif ($exception instanceof AuthorizationException) {
            $exception = new HttpException(403, $exception->getMessage());
        } elseif ($exception instanceof ValidationException && $exception->getResponse()) {
            return response()->json([
                'success' => false,
                'errors' => $exception->errors()
            ])->setStatusCode(400);
        }

        return $this->prepareJsonResponse($request, $exception);
    }
}
