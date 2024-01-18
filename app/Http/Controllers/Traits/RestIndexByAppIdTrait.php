<?php

namespace App\Http\Controllers\Traits;

use App\Http\Requests\IndexRequest;
use Illuminate\Http\JsonResponse;

trait RestIndexByAppIdTrait
{
    /**
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, ['app_id' => auth()->appId()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }
}
