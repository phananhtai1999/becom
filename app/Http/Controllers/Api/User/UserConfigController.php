<?php

namespace App\Http\Controllers\Api\User;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyUserConfigRequest;
use App\Http\Requests\UpdateMyUserConfigRequest;
use App\Http\Requests\UpdateUserConfigRequest;
use App\Http\Requests\UserConfigRequest;
use App\Http\Resources\UserConfigResourceCollection;
use App\Http\Resources\UserConfigResource;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Services\UserConfigService;
use Illuminate\Http\JsonResponse;

class UserConfigController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestStoreTrait, RestEditTrait;

    /**
     * UserConfigController constructor.
     * @param UserConfigService $service
     */
    public function __construct(UserConfigService $service)
    {
        $this->service = $service;
        $this->resourceCollectionClass = UserConfigResourceCollection::class;
        $this->resourceClass = UserConfigResource::class;
        $this->storeRequest = UserConfigRequest::class;
        $this->editRequest = UpdateUserConfigRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return JsonResponse
     */
    public function upsertMyUserConfig()
    {
        $model = $this->service->myUserConfig();

        if (empty($model)) {
            $request = app(MyUserConfigRequest::class);

            $model = $this->service->create(array_merge($request->all(), [
               'user_uuid' => auth()->user(),
            'app_id' => auth()->appId(),
            ]));
        } else {
            $request = app(UpdateMyUserConfigRequest::class);

            $this->service->update($model, array_merge($request->all(), [
               'user_uuid' => auth()->user(),
            'app_id' => auth()->appId(),
            ]));
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
