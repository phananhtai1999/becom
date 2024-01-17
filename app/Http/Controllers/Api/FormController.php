<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AcceptPublishFormRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyFormRequest;
use App\Http\Requests\SubmitContactForFormRequest;
use App\Http\Requests\UnpublishedFormRequest;
use App\Http\Requests\UpdateFormRequest;
use App\Http\Requests\UpdateMyFormRequest;
use App\Http\Requests\UpdateUnpublishedFormRequest;
use App\Http\Resources\FormResource;
use App\Http\Resources\FormResourceCollection;
use App\Models\Form;
use App\Services\ContactListService;
use App\Services\ContactService;
use App\Services\FormService;
use App\Services\MyFormService;
use Carbon\Carbon;
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        if ($request->get('contact_list_uuid')) {
            $contactList = $this->contactListService->findOneById($request->get('contact_list_uuid'));
            $userUuid = $contactList->user_uuid;
        }else{
            $userUuid = auth()->userId();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'publish_status' => Form::PUBLISHED_PUBLISH_STATUS,
            'user_uuid' => $userUuid,
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        $data = $request->except(['user_uuid']);

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
            'publish_status' => Form::PUBLISHED_PUBLISH_STATUS,
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
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

        $this->myService->update($model, $request->except(['user_uuid', 'publish_status']));

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
                "user_uuid" => $form->user_uuid,
                'app_id' => auth()->appId(),
            ]);

            $contact->contactLists()->attach($form->contact_list_uuid);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexUnpublishedForm(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request,
            ['publish_status' => Form::PENDING_PUBLISH_STATUS]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUnpublishedForm($id)
    {
        $model = $this->service->showFormForEditorById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UnpublishedFormRequest $request
     * @return JsonResponse
     */
    public function storeUnpublishedForm(UnpublishedFormRequest $request)
    {
        if ($request->get('contact_list_uuid')) {
            $contactList = $this->contactListService->findOneById($request->get('contact_list_uuid'));
            $userUuid = $contactList->user_uuid;
        }else{
            $userUuid = auth()->userId();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $userUuid,
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateUnpublishedFormRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editUnpublishedForm(UpdateUnpublishedFormRequest $request, $id)
    {
        $model = $this->service->showFormForEditorById($id);

        $data = $request->except('user_uuid');

        if ($request->get('contact_list_uuid') && $request->get('contact_list_uuid') != $model->contact_list_uuid) {
            $contactList = $this->contactListService->findOneById($request->get('contact_list_uuid'));
            $data = array_merge($request->all(), [
                'user_uuid' => $contactList->user_uuid,
            ]);
        }

        $this->service->update($model, $data);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param AcceptPublishFormRequest $request
     * @return JsonResponse
     */
    public function changeStatusForm(AcceptPublishFormRequest $request)
    {
        $FormUuids = $request->forms;
        foreach ($FormUuids as $FormUuid)
        {
            $model = $this->service->findOneById($FormUuid);
            $list_reason = $model->reject_reason;
            if ($request->get('publish_status') == Form::REJECT_PUBLISH_STATUS){
                $list_reason[] = [
                    'content' => $request->get('reject_reason'),
                    'created_at' => Carbon::now()
                ];
            }
            $this->service->update($model, [
                'publish_status' => $request->get('publish_status'),
                'reject_reason' => $list_reason
            ]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFormsDefault(IndexRequest $request)
    {
        $models = $this->service->getCollectionWithPaginationByCondition($request, [
            ['publish_status', Form::PUBLISHED_PUBLISH_STATUS],
            ['contact_list_uuid', null],
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showFormDefault($id)
    {
        $model = $this->service->showFormDefaultById($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
}
