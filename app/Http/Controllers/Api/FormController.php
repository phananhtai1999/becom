<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\FormRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyFormRequest;
use App\Http\Requests\SubmitContactForFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Requests\UpdateMyFormRequest;
use App\Http\Resources\FormResource;
use App\Http\Resources\FormResourceCollection;
use App\Services\ContactListService;
use App\Services\ContactService;
use App\Services\FormService;
use App\Services\MyFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;
    /**
     * @var MyFormService
     */
    protected $myService;

    protected $contactListService;

    protected $contactService;


    /**
     * @param FormService $service
     * @param MyFormService $myService
     */
    public function __construct(
        FormService $service,
        MyFormService $myService,
        ContactListService $contactListService,
        ContactService $contactService
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
        $this->contactService = $contactService;
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

    /**
     * @param SubmitContactForFormRequest $request
     * @return JsonResponse
     */
    public function submitContact(SubmitContactForFormRequest $request)
    {
        $form = $this->service->findOneById($request->get('form_uuid'));

        if (!$this->contactListService->checkContactExistsInContactList($form->contact_list_uuid, $request->email)) {
            $contact = $this->contactService->create([
                "first_name" => $request->get('first_name'),
                "last_name" => $request->get('last_name'),
                "email" => $request->get('email'),
                "phone" => $request->get('phone'),
                "user_uuid" => $form->user_uuid
            ]);

            $contact->contactLists()->attach($form->contact_list_uuid);
        }

        return $this->sendOkJsonResponse();
    }

}
