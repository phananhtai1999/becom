<?php

namespace App\Http\Controllers\Api\User;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateUserDetailRequest;
use App\Http\Requests\UpsertMyUserDetailRequest;
use App\Http\Requests\UserDetailRequest;
use App\Http\Resources\UserDetailResourceCollection;
use App\Http\Resources\UserDetailResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Models\UserDetail;
use App\Services\UserDetailService;
use Illuminate\Http\JsonResponse;

class UserDetailController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * UserDetailController constructor.
     * @param UserDetailService $service
     */
    public function __construct(UserDetailService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserDetailResourceCollection::class;
        $this->resourceClass = UserDetailResource::class;
        $this->storeRequest = UserDetailRequest::class;
        $this->editRequest = UpdateUserDetailRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function upsertMyUserDetail(UpsertMyUserDetailRequest $request)
    {
        $model = $this->service->myUserDetail();

        if (empty($model)) {
            $model = $this->service->create(array_merge($request->all(), [
                'user_uuid' => auth()->userId(),
                'app_id' => auth()->appId(),
            ]));
        } else {
            $this->service->update($model, array_merge($request->all(), [
                'user_uuid' => auth()->userId(),
                'app_id' => auth()->appId(),
            ]));
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
