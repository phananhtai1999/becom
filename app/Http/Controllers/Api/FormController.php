<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\FormRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Requests\UpdateMyFormRequest;
use App\Http\Resources\FormResource;
use App\Http\Resources\FormResourceCollection;
use App\Services\ContactListService;
use App\Services\FormService;
use App\Services\MyFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;
    /**
     * @var MyFormService
     */
    protected $myService;

    protected $contactListService;


    /**
     * @param FormService $service
     * @param MyFormService $myService
     */
    public function __construct(
        FormService $service,
        MyFormService $myService,
        ContactListService $contactListService
    )
    {
        $this->myService = $myService;
        $this->service = $service;
        $this->resourceCollectionClass = FormResourceCollection::class;
        $this->resourceClass = FormResource::class;
        $this->storeRequest = FormRequest::class;
        $this->editRequest = UpdateFormRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->contactListService = $contactListService;
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $request = app($this->storeRequest);

        $contactList = $this->contactListService->findOneById($request->get('contact_list_uuid'));

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $contactList->user_uuid
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $data = $request->except('user_uuid');

        if ($request->get('contact_list_uuid') && $request->get('contact_list_uuid') != $model->contact_list_uuid) {
            $contactList = $this->contactListService->findOneById($request->get('contact_list_uuid'));
            $data = array_merge($request->all(), [
                'user_uuid' => $contactList->user_uuid,
            ]);
        }

        $this->service->update($model, $data);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function indexMyForm(IndexRequest $request)
    {
        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData(
                $this->resourceCollectionClass,
                $this->myService->getCollectionWithPagination()
            )
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyForm($id)
    {
        $model = $this->myService->showMyForm($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyForm($id)
    {
        $this->myService->deleteMyForm($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param MyFormRequest $request
     * @return JsonResponse
     */
    public function storeMyForm(MyFormRequest $request)
    {
        $model = $this->myService->create( array_merge($request->all(), [
            'user_uuid' => auth()->user()->getKey()
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyFormRequest $request
     * @return JsonResponse
     */
    public function editMyForm(UpdateMyFormRequest $request, $id)
    {
        $model = $this->myService->showMyForm($id);

        $this->myService->update($model, $request->except('user_uuid'));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }



}
