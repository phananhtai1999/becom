<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Events\ActivityHistoryEvent;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyNoteRequest;
use App\Http\Requests\NoteRequest;
use App\Http\Requests\UpdateMyNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Resources\NoteResource;
use App\Http\Resources\NoteResourceCollection;
use App\Models\Note;
use App\Services\MyNoteService;
use App\Services\MyStatusService;
use App\Services\NoteService;

class NoteController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MyStatusService
     */
    protected $myService;

    /**
     * @param NoteService $service
     * @param MyNoteService $myService
     */
    public function __construct(
        NoteService   $service,
        MyNoteService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = NoteResourceCollection::class;
        $this->resourceClass = NoteResource::class;
        $this->storeRequest = NoteRequest::class;
        $this->editRequest = UpdateNoteRequest::class;
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
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        //Add activity history
        ActivityHistoryEvent::dispatch($model, Note::NOTE_TYPE, Note::NOTE_CREATED_ACTION);

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
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        //Add activity history
        if (!empty($request->all())) {
            ActivityHistoryEvent::dispatch($model, Note::NOTE_TYPE, Note::NOTE_UPDATED_ACTION);
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
        ActivityHistoryEvent::dispatch($getDeletedRecord, Note::NOTE_TYPE, Note::NOTE_DELETED_ACTION);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param MyNoteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyNote(MyNoteRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        //Add activity history
        ActivityHistoryEvent::dispatch($model, Note::NOTE_TYPE, Note::NOTE_CREATED_ACTION);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyNote($id)
    {
        $model = $this->myService->showMyNote($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyNoteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyNote(UpdateMyNoteRequest $request, $id)
    {
        $model = $this->myService->showMyNote($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ]));

        //Add activity history
        if (!empty($request->all())) {
            ActivityHistoryEvent::dispatch($model, Note::NOTE_TYPE, Note::NOTE_UPDATED_ACTION);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyNote($id)
    {
        $this->myService->deleteMyNote($id);
        $getDeletedRecord = $this->service->withTrashed($id);

        //Add activity history
        ActivityHistoryEvent::dispatch($getDeletedRecord, Note::NOTE_TYPE, Note::NOTE_DELETED_ACTION);

        return $this->sendOkJsonResponse();
    }
}
