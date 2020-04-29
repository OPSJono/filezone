<?php

namespace App\Http\Controllers;

use App\Interfaces\ApiResponseInterface;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var ApiResponseInterface
     */
    protected ApiResponseInterface $apiResponse;

    /**
     * Controller constructor.
     * @param Request $request
     * @param ApiResponseInterface $apiResponse
     */
    public function __construct(Request $request, ApiResponseInterface $apiResponse)
    {
        $this->request = $request;
        $this->apiResponse = $apiResponse;
    }
}
