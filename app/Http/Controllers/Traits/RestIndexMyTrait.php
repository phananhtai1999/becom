<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;

trait RestIndexMyTrait
{
    /**
     * @return JsonResponse
     */
    public function indexMy()
    {
        app($this->indexRequest);

        $models = $this->myService->getCollectionWithPagination();

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
