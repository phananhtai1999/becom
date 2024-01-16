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
use App\Http\Requests\UpdateMyContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactResourceCollection;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Models\Permission;
use App\Models\PlatformPackage;
use App\Services\ContactListService;
use App\Services\ContactService;
use App\Services\MyContactListService;
use App\Services\MyContactService;
use App\Services\StatusService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class ContactController extends AbstractRestAPIController
{
    use RestShowTrait, RestDestroyTrait;

    /**
     * @var MyContactService
     */
    protected $myService;

    /**
     * @var MyContactListService
     */
    protected $myContactListService;

    /**
     * @var ContactListService
     */
    protected $contactListService;

    /**
     * @var
     */
    protected $statusService;

    /**
     * @param ContactService $service
     * @param MyContactService $myService
     * @param MyContactListService $myContactListService
     * @param ContactListService $contactListService
     * @param StatusService $statusService
     */
    public function __construct(
        ContactService       $service,
        MyContactService     $myService,
        MyContactListService $myContactListService,
        ContactListService   $contactListService,
        StatusService        $statusService
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
        $this->contactListService = $contactListService;
        $this->statusService = $statusService;
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request)
    {
        try {
            $models = $this->service->search($request->search, $request->search_by)->paginate(
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1')
            );

            //Check Active Status
            $checkActiveStatus = $this->service->checkActiveStatus($models);

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData($this->resourceCollectionClass, $checkActiveStatus)
            );
        } catch (\ValueError|\ErrorException $error) {
            return $this->sendValidationFailedJsonResponse();
        }
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
            'status_uuid' => $request->get('status_uuid') ?: optional($this->statusService->selectStatusDefault($request->get('user_uuid') ?? auth()->userId()))->uuid
        ]));

        //Add Pivot contact_company_position
        $pivot = $this->addPivotContactCompanyPosition($request->all());

        $model->contactLists()->attach($request->get('contact_list', []));
        $model->reminds()->attach($request->get('remind', []));
        $model->companies()->attach($pivot);

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

        $this->service->update($model, array_merge($request->except("points"), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        //Add Pivot contact_company_position
        $pivot = $this->addPivotContactCompanyPosition($request->all());

        $model->contactLists()->sync($request->contact_list ?? $model->contactLists);
        $model->reminds()->sync($request->remind ?? $model->reminds);
        $model->companies()->sync($pivot);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexMyContact(IndexRequest $request)
    {
        if (!Gate::allows('permission', config('api.contact.index'))) {
            return $this->sendJsonResponse(false, 'You need to upgrade platform package', ['data' => $this->getPlatformByPermission(config('api.contact.index'))], 403);
        }
        try {
            $models = $this->myService->search($request->search, $request->search_by)->paginate(
                $request->get('per_page', '15'),
                $request->get('columns', '*'),
                $request->get('page_name', 'page'),
                $request->get('page', '1')
            );

            //Check Active Status
            $checkActiveStatus = $this->myService->checkMyActiveStatus($models);

            return $this->sendOkJsonResponse(
                $this->service->resourceCollectionToData(
                    $this->resourceCollectionClass,
                    $checkActiveStatus
                )
            );
        } catch (\ValueError|\ErrorException $error) {
            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @param MyContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMyContact(MyContactRequest $request)
    {
        if (!Gate::allows('permission', config('api.contact.create'))) {
            return $this->sendJsonResponse(false, 'You need to upgrade platform package', ['data' => $this->getPlatformByPermission(config('api.contact.create'))], 403);
        }

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'status_uuid' => $request->get('status_uuid') ?: optional($this->statusService->selectStatusDefault(auth()->userId()))->uuid
        ]));

        //Add Pivot contact_company_position
        $pivot = $this->addPivotContactCompanyPosition($request->all());

        $model->contactLists()->attach($request->get('contact_list', []));
        $model->reminds()->attach($request->get('remind', []));
        $model->companies()->attach($pivot);

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
        if (!Gate::allows('permission', config('api.contact.show'))) {
            return $this->sendJsonResponse(false, 'You need to upgrade platform package', ['data' => $this->getPlatformByPermission(config('api.contact.show'))], 403);
        }
        $model = $this->myService->findMyContactByKeyOrAbort($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyContactRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function editMyContact(UpdateMyContactRequest $request, $id)
    {
        if (!Gate::allows('permission', config('api.contact.edit'))) {
            return $this->sendJsonResponse(false, 'You need to upgrade platform package', ['data' => $this->getPlatformByPermission(config('api.contact.edit'))], 403);
        }
        $model = $this->myService->findMyContactByKeyOrAbort($id);

        $this->service->update($model, array_merge($request->except("points"), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        //Add Pivot contact_company_position
        $pivot = $this->addPivotContactCompanyPosition($request->all());

        $model->contactLists()->sync($request->contact_list ?? $model->contactLists);
        $model->reminds()->sync($request->remind ?? $model->reminds);
        $model->companies()->sync($pivot);

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
        if (!Gate::allows('permission', config('api.contact.delete'))) {
            return $this->sendJsonResponse(false, 'You need to upgrade platform package', ['data' => $this->getPlatformByPermission(config('api.contact.delete'))], 403);
        }
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

    public function pointsContactChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        if (empty($request->get('contact_list_uuid')) || $this->contactListService->checkContactListByUuid($request->get('contact_list_uuid'))) {
            $totalPointsContact = $this->service->getTotalPointsContactByContactList($startDate, $endDate, $request->get('contact_list_uuid'));
            $pointsContactChart = $this->service->getPointsContactChartByContactList($groupBy, $startDate, $endDate, $request->get('contact_list_uuid'));
            return $this->sendOkJsonResponse([
                'data' => $pointsContactChart,
                'total' => [
                    'points' => $totalPointsContact,
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['contact_list_uuid' => __('messages.contact_list_uuid_invalid')]]);
    }

    /**
     * @param ChartRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myPointsContactChart(ChartRequest $request)
    {
        $startDate = $request->get('start_date', Carbon::today());
        $endDate = $request->get('end_date', Carbon::today());
        $groupBy = $request->get('group_by', 'hour');

        if (empty($request->get('contact_list_uuid')) || $this->myContactListService->checkMyContactList($request->get('contact_list_uuid'))) {
            $totalMyPointsContact = $this->myService->getTotalPointsContactByMyContactList($startDate, $endDate, $request->get('contact_list_uuid'));
            $pointsContactChart = $this->myService->getPointsContactChartByMyContactList($groupBy, $startDate, $endDate, $request->get('contact_list_uuid'));
            return $this->sendOkJsonResponse([
                'data' => $pointsContactChart,
                'total' => [
                    'points' => $totalMyPointsContact,
                ]
            ]);
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ['contact_list_uuid' => __('messages.contact_list_uuid_invalid')]]);
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function customFilterDefault()
    {
        return $this->sendOkJsonResponse([
            'data' => config('customfilterdefault')
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectAllContact()
    {
        try {
            $models = $this->service->filteringByCustomContactField()->get()->pluck('uuid');

            return $this->sendOkJsonResponse([
                'data' => [
                    'uuid' => $models
                ]
            ]);
        } catch (\ValueError|\ErrorException $error) {
            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectAllMyContact()
    {
        try {
            $models = $this->myService->filteringByMyCustomContactField()->get()->pluck('uuid');

            return $this->sendOkJsonResponse([
                'data' => [
                    'uuid' => $models
                ]
            ]);
        } catch (\ValueError|\ErrorException $error) {
            return $this->sendValidationFailedJsonResponse();
        }
    }

    /**
     * @param $arrayContactCompanyPosition
     * @return array
     */
    public function addPivotContactCompanyPosition($arrayContactCompanyPosition)
    {
        $arrayData = [];
        if (!empty($arrayContactCompanyPosition['positions'])) {
            foreach (array_unique($arrayContactCompanyPosition['positions']) as $position) {
                $arrayData[] = [
                    "company_uuid" => $arrayContactCompanyPosition['company_uuid'],
                    "position_uuid" => $position,
                    "department_uuid" => !empty($arrayContactCompanyPosition['department_uuid']) ? $arrayContactCompanyPosition['department_uuid'] : null,
                ];
            }
        } elseif (!empty($arrayContactCompanyPosition['company_uuid'])) {
            $arrayData[] = [
                "company_uuid" => $arrayContactCompanyPosition['company_uuid'],
                "position_uuid" => null,
                "department_uuid" => !empty($arrayContactCompanyPosition['department_uuid']) ? $arrayContactCompanyPosition['department_uuid'] : null,
            ];
        }

        return $arrayData;
    }
}
