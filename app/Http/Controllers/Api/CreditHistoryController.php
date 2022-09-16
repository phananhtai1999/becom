<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\CreditHistoryRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateCreditHistoryRequest;
use App\Http\Resources\CreditHistoryResource;
use App\Http\Resources\CreditHistoryResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Services\CreditHistoryService;

class CreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    /**
     * @param CreditHistoryService $service
     */
    public function __construct(CreditHistoryService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = CreditHistoryResourceCollection::class;
        $this->resourceClass = CreditHistoryResource::class;
        $this->storeRequest = CreditHistoryRequest::class;
        $this->editRequest = UpdateCreditHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if (empty($request->user_uuid)) {
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        } else {
            $data = $request->all();
        }

        $model = $this->service->create($data);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
