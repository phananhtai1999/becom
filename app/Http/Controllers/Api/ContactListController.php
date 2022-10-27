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
use App\Services\ContactService;
use App\Services\MyContactListService;

class ContactListController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @var
     */
    protected $contactService;

    /**
     * @param ContactListService $service
     * @param MyContactListService $myService
     * @param ContactService $contactService
     */
    public function __construct(
        ContactListService $service,
        MyContactListService $myService,
        ContactService $contactService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->contactService = $contactService;
        $this->resourceCollectionClass = ContactListResourceCollection::class;
        $this->resourceClass = ContactListResource::class;
        $this->storeRequest = ContactListRequest::class;
        $this->editRequest = UpdateContactListRequest::class;
        $this->indexRequest = IndexRequest::class;
    }

    /**
     * @param ContactListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function storeAndImportFile(ContactListRequest $request)
    {
        $file = $request->file;
        if (!empty($file)) {
            try {
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'xlsx' || $extension == 'xcsv') {
                    $import = $this->contactService->importExcelOrCsvFile($file);
                } else {
                    $import = $this->contactService->importJsonFile($file);
                }
                if (is_array($import)) {
                    if (empty($request->user_uuid)) {
                        $data = array_merge($request->all(), [
                            'user_uuid' => auth()->user()->getkey(),
                        ]);
                    } else {
                        $data = $request->all();
                    }

                    $model = $this->service->create($data);
                    $model->contacts()->attach(array_merge($request->get('contact', []), $import['data']));
                    if ($import['have_error_data'] === true) {

                        return $this->sendCreatedJsonResponse([
                                'data' => $this->service->resourceToData($this->resourceClass, $model)['data'],
                                'errors' => $import['errors'],
                                'error_data' => $import['error_data'],
                                'slug' => $import['slug']
                            ]
                        );
                    }

                    return $this->sendCreatedJsonResponse(
                        $this->service->resourceToData($this->resourceClass, $model)
                    );
                } elseif ($import === false) {

                    return $this->sendValidationFailedJsonResponse();
                }
            } catch (\ErrorException $errorException) {

                return $this->sendValidationFailedJsonResponse();
            }
        }

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
     * @param UpdateContactListRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function edit($id, UpdateContactListRequest $request)
    {
        $model = $this->service->findOrFailById($id);
        $file = $request->file;
        if (!empty($file)) {
            try {
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'xlsx' || $extension == 'xcsv') {
                    $import = $this->contactService->importExcelOrCsvFile($file);
                } else {
                    $import = $this->contactService->importJsonFile($file);
                }
                if (is_array($import)) {
                    $this->service->update($model, $request->all());
                    $contactUuid = $this->service->findContactKeyByContactList($model);
                    $model->contacts()->sync(array_merge($request->get('contact', $contactUuid), $contactUuid, $import['data']));

                    if ($import['have_error_data'] === true) {

                        return $this->sendOkJsonResponse([
                                'data' => $this->service->resourceToData($this->resourceClass, $model)['data'],
                                'errors' => $import['errors'],
                                'error_data' => $import['error_data'],
                                'slug' => $import['slug']
                            ]
                        );
                    }

                    return $this->sendOkJsonResponse(
                        $this->service->resourceToData($this->resourceClass, $model)
                    );
                } elseif ($import === false) {

                    return $this->sendValidationFailedJsonResponse();
                }
            } catch (\ErrorException $errorException) {

                return $this->sendValidationFailedJsonResponse();
            }
        }
        $this->service->update($model, $request->all());
        $contactUuid = $this->service->findContactKeyByContactList($model);
        $model->contacts()->sync($request->get('contact', $contactUuid));

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
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function storeMyContactListAndImportFile(MyContactListRequest $request)
    {
        $file = $request->file;
        if (!empty($file)) {
            try {
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'xlsx' || $extension == 'xcsv') {
                    $import = $this->contactService->importExcelOrCsvFile($file);
                } else {
                    $import = $this->contactService->importJsonFile($file);
                }
                if (is_array($import)) {
                    $model = $this->service->create(array_merge($request->all(), [
                        'user_uuid' => auth()->user()->getkey(),
                    ]));
                    $model->contacts()->attach(array_merge($request->get('contact', []), $import['data']));
                    if ($import['have_error_data'] === true) {

                        return $this->sendCreatedJsonResponse([
                                'data' => $this->service->resourceToData($this->resourceClass, $model)['data'],
                                'errors' => $import['errors'],
                                'error_data' => $import['error_data'],
                                'slug' => $import['slug']
                            ]
                        );
                    }

                    return $this->sendCreatedJsonResponse(
                        $this->service->resourceToData($this->resourceClass, $model)
                    );
                } elseif ($import === false) {

                    return $this->sendValidationFailedJsonResponse();
                }
            } catch (\ErrorException $errorException) {

                return $this->sendValidationFailedJsonResponse();
            }
        }
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
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function editMyContactList(UpdateMyContactListRequest $request, $id)
    {
        $model = $this->myService->findMyContactListByKeyOrAbort($id);
        $file = $request->file;
        if (!empty($file)) {
            try {
                $extension = $file->getClientOriginalExtension();
                if ($extension == 'xlsx' || $extension == 'xcsv') {
                    $import = $this->contactService->importExcelOrCsvFile($file);
                } else {
                    $import = $this->contactService->importJsonFile($file);
                }
                if (is_array($import)) {
                    $this->service->update($model, array_merge($request->all(), [
                        'user_uuid' => auth()->user()->getkey(),
                    ]));
                    $contactUuid = $this->service->findContactKeyByContactList($model);
                    $model->contacts()->sync(array_merge($request->get('contact', $contactUuid), $contactUuid, $import['data']));

                    if ($import['have_error_data'] === true) {

                        return $this->sendOkJsonResponse([
                                'data' => $this->service->resourceToData($this->resourceClass, $model)['data'],
                                'errors' => $import['errors'],
                                'error_data' => $import['error_data'],
                                'slug' => $import['slug']
                            ]
                        );
                    }

                    return $this->sendOkJsonResponse(
                        $this->service->resourceToData($this->resourceClass, $model)
                    );
                } elseif ($import === false) {

                    return $this->sendValidationFailedJsonResponse();
                }
            } catch (\ErrorException $errorException) {

                return $this->sendValidationFailedJsonResponse();
            }
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));
        $contactUuid = $this->service->findContactKeyByContactList($model);
        $model->contacts()->sync($request->get('contact', $contactUuid));

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

    /**
     * @param $id
     * @param $contact_id
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function removeContactFromContactList($id, $contact_id)
    {
        $model = $this->service->findOrFailById($id);
        $model->contacts()->detach($contact_id);
        return $this->sendOkJsonResponse();
    }

    /**
     * @param $id
     * @param $contact_id
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function removeMyContactFromContactList($id, $contact_id)
    {
        $model = $this->myService->findMyContactListByKeyOrAbort($id);
        $model->contacts()->detach($contact_id);
        return $this->sendOkJsonResponse();
    }

}
