<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestIndexByUserIdAndAppIdTrait
{
    /**
     * @return JsonResponse
     */
    public function indexMy()
    {
        app($this->indexRequest);

        $models = $this->service->getCollectionByUserIdAndAppIdWithPagination();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
