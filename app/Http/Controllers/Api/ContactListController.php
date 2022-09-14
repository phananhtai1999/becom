<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ContactListRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\UpdateContactListRequest;
use App\Http\Resources\ContactListResource;
use App\Http\Resources\ContactListResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Services\ContactListService;

class ContactListController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    public function __construct(ContactListService $service)
    {
        $this->service = $service;
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

        $model = $this->service->create($request->all());

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
}
