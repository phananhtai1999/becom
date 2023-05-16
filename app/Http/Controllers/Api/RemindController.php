<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\ActivityHistoryEvent;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyRemindRequest;
use App\Http\Requests\RemindRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\UpdateMyRemindRequest;
use App\Http\Requests\UpdateRemindRequest;
use App\Http\Resources\RemindResource;
use App\Http\Resources\RemindResourceCollection;
use App\Models\Remind;
use App\Services\MyRemindService;
use App\Services\MyStatusService;
use App\Services\RemindService;
use Illuminate\Support\Facades\Gate;

class RemindController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @param RemindService $service
     * @param MyRemindService $myService
     */
    public function __construct(
        RemindService   $service,
        MyRemindService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = RemindResourceCollection::class;
        $this->resourceClass = RemindResource::class;
        $this->storeRequest = RemindRequest::class;
        $this->editRequest = UpdateRemindRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->user()->getKey()
        ]));

        $model->contacts()->attach($request->get('contact', []));

        //Add activity history
        ActivityHistoryEvent::dispatch($model, Remind::REMIND_TYPE, Remind::REMIND_CREATED_ACTION);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->user()->getKey()
        ]));

        $model->contacts()->sync($request->contact ?? $model->contacts);

        //Add activity history
        if (!empty($request->all())) {
            ActivityHistoryEvent::dispatch($model, Remind::REMIND_TYPE, Remind::REMIND_UPDATED_ACTION);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->service->destroy($id);
        $getDeletedRecord = $this->service->withTrashed($id);

        //Add activity history
        ActivityHistoryEvent::dispatch($getDeletedRecord, Remind::REMIND_TYPE, Remind::REMIND_DELETED_ACTION);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param MyRemindRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyRemind(MyRemindRequest $request)
    {
//        if (!Gate::allows('permission', config('api.remind.create'))) {
//            return $this->sendJsonResponse(false, 'You need to upgrade platform package', [], 403);
//        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contacts()->attach($request->get('contact', []));

        //Add activity history
        ActivityHistoryEvent::dispatch($model, Remind::REMIND_TYPE, Remind::REMIND_CREATED_ACTION);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyRemind($id)
    {
        $model = $this->myService->showMyRemind($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyRemindRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyRemind(UpdateMyRemindRequest $request, $id)
    {
        $model = $this->myService->showMyRemind($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contacts()->sync($request->contact ?? $model->contacts);

        //Add activity history
        if (!empty($request->all())) {
            ActivityHistoryEvent::dispatch($model, Remind::REMIND_TYPE, Remind::REMIND_UPDATED_ACTION);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyRemind($id)
    {
        $this->myService->deleteMyRemind($id);
        $getDeletedRecord = $this->service->withTrashed($id);

        //Add activity history
        ActivityHistoryEvent::dispatch($getDeletedRecord, Remind::REMIND_TYPE, Remind::REMIND_DELETED_ACTION);

        return $this->sendOkJsonResponse();
    }
}
