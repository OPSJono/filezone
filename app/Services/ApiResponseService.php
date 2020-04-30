<?php

namespace App\Services;

use App\Interfaces\ApiResponseInterface;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiResponseService implements ApiResponseInterface
{
    protected array $headers = [];
    protected int $responseCode = 200;
    protected array $errors = [];
    protected array $success = [];

    public function addHeaders(array $headers = []) : void
    {
        foreach ($headers as $key => $value) {
            $this->headers[$key] = $value;
        }

    }

    public function hasHeaders() : bool
    {
        return !empty($this->headers);
    }

    public function setResponseCode(int $code = 200) : void
    {
        $this->responseCode = $code;
    }

    public function hasErrors() : bool
    {
        return !empty($this->errors);
    }

    public function setError($error) : void
    {
        $this->errors[] = $error;
    }

    public function setErrors(array $errors) : void
    {
        foreach ($errors as $error) {
            $this->setError($error);
        }
    }

    public function setValidationErrors(array $errors) : void
    {
        foreach($errors as $key => $value) {
            $this->errors[$key] = $value;
        }
    }

    public function setGeneralError(string $error) : void
    {
        $this->errors['general'] = [$error];
        if($this->responseCode == 200) {
            $this->setResponseCode(500);
        }
    }

    public function handleErrors($data) : void
    {
        switch (true) {
            case $data instanceof ModelNotFoundException:
            case $data instanceof NotFoundHttpException:
                $this->setError($data->getMessage());
                $this->setResponseCode(404);
                break;
            case $data instanceof ValidationException:
                $this->setError($data->getMessage());
                $this->setResponseCode(400);
                break;
            case $data instanceof Exception:
                $this->setError($data->getMessage());
                $this->setResponseCode(500);
                break;
            case $data instanceof Validator:
                $this->setValidationErrors($data->messages()->toArray());
                $this->setResponseCode(400);
                break;
            case is_array($data):
                $this->setErrors($data);
                $this->setResponseCode(400);
                break;
            default:
                $this->setError($data);
                $this->setResponseCode(500);
                break;
        }
    }

    public function setSuccess($data) : void
    {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $this->success = $data;
    }

    public function returnResponse() : JsonResponse
    {
        $response = response();

        if($this->hasErrors()) {
            $response = $response->json([
                'success' => false,
                'errors' => $this->errors
            ]);
        } else {
            $response = $response->json([
                'success' => true,
                'data' => $this->success
            ]);
        }

        if($this->hasHeaders()) {
            foreach($this->headers as $key => $value) {
                $response = $response->header($key, $value, true);
            }
        }

        return $response->setStatusCode($this->responseCode);
    }

}
