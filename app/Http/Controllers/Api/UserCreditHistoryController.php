<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UserCreditHistoryRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\UserCreditHistoryResource;
use App\Http\Resources\UserCreditHistoryResourceCollection;
use App\Services\UserCreditHistoryService;
use App\Services\MyUserCreditHistoryService;
use App\Services\UserService;

class UserCreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait;

    /**
     * @var MyUserCreditHistoryService
     */
    protected $myService;

    /**
     * @var
     */
    protected $userService;

    /**
     * @param UserCreditHistoryService $service
     * @param MyUserCreditHistoryService $myService
     * @param UserService $userService
     */
    public function __construct(
        UserCreditHistoryService $service,
        MyUserCreditHistoryService $myService,
        UserService $userService
    )
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserCreditHistoryResourceCollection::class;
        $this->resourceClass = UserCreditHistoryResource::class;
        $this->storeRequest = UserCreditHistoryRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->myService = $myService;
        $this->userService = $userService;
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
                'add_by_uuid' => auth()->user()->getkey()
            ]);
        } else {
            $data = array_merge($request->all(), [
                'add_by_uuid' => auth()->user()->getkey()
            ]);
        }

        $user = $this->userService->findOrFailById($data['user_uuid']);

        if($data['credit'] + $user->credit < 0)
        {
            return $this->sendValidationFailedJsonResponse();
        } else {
            $model = $this->service->create($data);
            $this->service->updateUserCredit($model->user, $model->credit, $model->user->credit);
        }

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
