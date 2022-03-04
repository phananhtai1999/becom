<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestIndexTrait
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $models = $this->service->getCollectionWithPagination();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
