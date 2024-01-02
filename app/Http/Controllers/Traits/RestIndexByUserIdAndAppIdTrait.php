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
        $request = app($this->indexRequest);

        $models = $this->service->getCollectionByUserIdAndAppIdWithPagination($request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
