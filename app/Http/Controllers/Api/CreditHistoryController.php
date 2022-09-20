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
use App\Services\MyCreditHistoryService;

class CreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    /**
     * @var MyCreditHistoryService
     */
    protected $myService;

    /**
     * @param CreditHistoryService $service
     * @param MyCreditHistoryService $myService
     */
    public function __construct(CreditHistoryService $service, MyCreditHistoryService $myService)
    {
        $this->service = $service;
        $this->resourceCollectionClass = CreditHistoryResourceCollection::class;
        $this->resourceClass = CreditHistoryResource::class;
        $this->storeRequest = CreditHistoryRequest::class;
        $this->editRequest = UpdateCreditHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->myService = $myService;
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

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyCreditHistory(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination(
                    $request->get('per_page', '15'),
                    $request->get('page', '1'),
                    $request->get('columns', '*'),
                    $request->get('page_name', 'page'),
                )
            )
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyCreditHistory($id)
    {
        $model = $this->myService->showMyCreditHistory($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
