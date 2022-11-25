<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Requests\ChartRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ImportExcelOrCsvFileRequest;
use App\Http\Requests\ImportJsonFileRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResourceCollection;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Services\ContactService;
use App\Services\MyContactListService;
use App\Services\MyContactService;
use Carbon\Carbon;

class ContactController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait;

    /**
     * @var
     */
    protected $myService;

    /**
     * @var
     */
    protected $myContactListService;

    /**
     * @param ContactService $service
     * @param MyContactService $myService
     * @param MyContactListService $myContactListService
     */
    public function __construct(
        ContactService $service,
        MyContactService $myService,
        MyContactListService $myContactListService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->myContactListService = $myContactListService;
        $this->resourceCollectionClass = ContactResourceCollection::class;
        $this->resourceClass = ContactResource::class;
        $this->storeRequest = ContactRequest::class;
        $this->editRequest = UpdateContactRequest::class;
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

        $model->contactLists()->attach($request->get('contact_list', []));

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

        $this->service->update($model, $request->except("points"));

        $contactListUuid = $this->service->findContactListKeyByContact($model);

        if ($contactListUuid == null) {
            $model->contactLists()->sync($request->get('contact_list', []));
        } else {
            $model->contactLists()->sync($request->get('contact_list', $contactListUuid));
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
    public function indexMyContact(IndexRequest $request)
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
     * @param MyContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyContact(MyContactRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $model->contactLists()->attach($request->get('contact_list', []));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyContact($id)
    {
        $model = $this->myService->findMyContactByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateContactRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyContact(UpdateContactRequest $request, $id)
    {
        $model = $this->myService->findMyContactByKeyOrAbort($id);

        $this->service->update($model, array_merge($request->except("points"), [
            'user_uuid' => auth()->user()->getkey(),
        ]));

        $contactListUuid = $this->service->findContactListKeyByContact($model);

        if ($contactListUuid == null) {
            $model->contactLists()->sync($request->get('contact_list', []));
        } else {
            $model->contactLists()->sync($request->get('contact_list', $contactListUuid));
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMyContact($id)
    {
        $this->myService->deleteMyContactByKey($id);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param ImportExcelOrCsvFileRequest $request
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function importExcelOrCsvFile(ImportExcelOrCsvFileRequest $request)
    {
        try {
            $import = $this->service->importExcelOrCsvFile($request->file);
            if (is_array($import)) {
                if ($import['have_error_data'] === true) {
                    return $this->sendOkJsonResponse([
                        'data' => $import['success_data'],
                        'errors' => $import['errors'],
                        'error_data' => $import['error_data'],
                        'slug' => $import['slug']
                    ]);
                }

                return $this->sendOkJsonResponse(['data' => $import['success_data']]);
            } elseif ($import === false) {

                return $this->sendValidationFailedJsonResponse();
            }
        } catch (\ErrorException $errorException) {

            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @param ImportJsonFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importJsonFile(ImportJsonFileRequest $request)
    {
        try {
            $import = $this->service->importJsonFile($request->file);
            if ($import['have_error_data'] === true) {
                return $this->sendOkJsonResponse([
                    'errors' => $import['errors'],
                    'error_data' => $import['error_data'],
                    'slug' => $import['slug']
                ]);
            }

            return $this->sendOkJsonResponse();
        } catch (\ErrorException $errorException) {

            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function templateExcel()
    {
        $url = "template_excel/" . "Contact.xlsx";

        return $this->sendOkJsonResponse([
            'data' => [
                "slug" => $url,
            ]
        ]);
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myContactChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');
        $totalMyContact = $this->myService->myTotalContact($startDate, $endDate);
        $totalMyContactList = $this->myContactListService->myTotalContactList($startDate, $endDate);
        $data = $this->myService->myContactChart($groupBy, $startDate, $endDate);

        return $this->sendOkJsonResponse([
            'data' => $data,
            'total' => [
                'contact' => $totalMyContact,
                'list' => $totalMyContactList,
            ]
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function dynamicContentContact()
    {
        return $this->sendOkJsonResponse([
            'data' => [
                'allow_fields' => config('dynamiccontentcontact')
            ]
        ]);
    }
}
