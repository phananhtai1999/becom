<?php

namespace App\Http\Controllers;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;

class ShopController extends AbstractRestAPIController
{
    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }

    public function myProduct(IndexRequest $request): JsonResponse
    {
        try {
            $data = $this->service->myProduct($request);

            return $this->sendOkJsonResponse($data);
        } catch (\Exception $exception) {

            return $this->sendBadRequestJsonResponse(['message' => $exception->getMessage()]);
        }
    }

    public function myCategory(IndexRequest $request): JsonResponse
    {
        try {
            $data = $this->service->myCategory($request);

            return $this->sendOkJsonResponse($data);
        } catch (\Exception $exception) {

            return $this->sendBadRequestJsonResponse(['message' => $exception->getMessage()]);
        }
    }
}
