<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateUserCreditHistoryRequest;
use App\Http\Requests\UserCreditHistoryRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Resources\UserCreditHistoryResource;
use App\Http\Resources\UserCreditHistoryResourceCollection;
use App\Services\UserCreditHistoryService;
use App\Services\MyUserCreditHistoryService;
class UserCreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    /**
     * @param UserCreditHistoryService $service
     */
    public function __construct(
        UserCreditHistoryService $service,
        MyUserCreditHistoryService $myService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserCreditHistoryResourceCollection::class;
        $this->resourceClass = UserCreditHistoryResource::class;
        $this->storeRequest = UserCreditHistoryRequest::class;
        $this->editRequest = UpdateUserCreditHistoryRequest::class;
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
    public function indexMyUserCreditHistory(IndexRequest $request)
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
    public function showMyUserCreditHistory($id)
    {
        $model = $this->myService->showMyUserCreditHistory($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
