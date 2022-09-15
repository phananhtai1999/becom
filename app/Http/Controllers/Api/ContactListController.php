<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ContactListRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyContactListRequest;
use App\Http\Requests\UpdateContactListRequest;
use App\Http\Requests\UpdateMyContactListRequest;
use App\Http\Resources\ContactListResource;
use App\Http\Resources\ContactListResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Services\ContactListService;
use App\Services\MyContactListService;

class ContactListController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @param ContactListService $service
     * @param MyContactListService $myService
     */
    public function __construct(
        ContactListService $service,
        MyContactListService $myService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->resourceCollectionClass = ContactListResourceCollection::class;
        $this->resourceClass = ContactListResource::class;
        $this->storeRequest = ContactListRequest::class;
        $this->editRequest = UpdateContactListRequest::class;
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

        if (empty($request->user_uuid)) {
            $data = array_merge($request->all(), [
                'user_uuid' => auth()->user()->getkey(),
            ]);
        } else {
            $data = $request->all();
        }

        $model = $this->service->create($data);

        $model->contacts()->attach($request->get('contact', []));

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

        $this->service->update($model, $request->all());

        $contactUuid = $this->service->findContactKeyByContactList($model);

        if ($contactUuid == null) {
            $model->contacts()->sync($request->get('contact', []));
        } else {
            $model->contacts()->sync($request->get('contact', $contactUuid));
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyContactList(IndexRequest $request)
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
     * @param MyContactListRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyContactList(MyContactListRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contacts()->attach($request->get('contact', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyContactList($id)
    {
        $model = $this->myService->findMyContactListByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyContactListRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyContactList(UpdateMyContactListRequest $request, $id)
    {
        $model = $this->myService->findMyContactListByKeyOrAbort($id);

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $contactUuid = $this->service->findContactKeyByContactList($model);

        if ($contactUuid == null) {
            $model->contacts()->sync($request->get('contact', []));
        } else {
            $model->contacts()->sync($request->get('contact', $contactUuid));
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyContactList($id)
    {
        $this->myService->deleteMyContactListByKey($id);

        return $this->sendOkJsonResponse();
    }

}
