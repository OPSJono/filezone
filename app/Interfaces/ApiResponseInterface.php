<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;

interface ApiResponseInterface {
    function addHeaders(array $headers = []) : void;
    function setCORS() : void;
    function hasHeaders() : bool;
    function setResponseCode(int $code = 200) : void;
    function hasErrors() : bool;
    function setError($data) : void;
    function setErrors(array $data) : void;
    function setValidationErrors(array $data) : void;
    function setGeneralError(string $error) : void;
    function handleErrors($data) : void;

    function setSuccess($data) : void;

    function returnResponse() : JsonResponse;
}
