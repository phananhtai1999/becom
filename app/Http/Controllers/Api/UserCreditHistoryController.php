<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UserCreditHistoryRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\UserCreditHistoryResource;
use App\Http\Resources\UserCreditHistoryResourceCollection;
use App\Services\UserCreditHistoryService;
use App\Services\MyUserCreditHistoryService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class UserCreditHistoryController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

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
                'user_uuid' => auth()->user(),
                'add_by_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]);
        } else {
            $data = array_merge($request->all(), [
                'add_by_uuid' => auth()->user(),
                'app_id' => auth()->appId(),
            ]);
        }

        $user = $this->userService->findOrFailById($data['user_uuid']);

        if($data['credit'] + $user->credit < 0)
        {
            return $this->sendValidationFailedJsonResponse();
        } else {
            DB::beginTransaction();

            try {
                $model = $this->service->create($data);
                $this->service->updateUserCredit($model->user, $model->credit, $model->user->credit);

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();

                return $this->sendValidationFailedJsonResponse();
            }
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
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
