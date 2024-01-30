<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Requests\AddDepartmentForBusinessRequest;
use App\Http\Requests\AddDepartmentForLocationRequest;
use App\Http\Requests\DepartmentRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MyDepartmentRequest;
use App\Http\Requests\OptionDeleteBusinuessRequest;
use App\Http\Requests\RemoveTeamFromDepartmentRequest;
use App\Http\Requests\SetAppForDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\UpdateMyDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\DepartmentResourceCollection;
use App\Models\UserConfig;
use App\Services\BusinessManagementService;
use App\Services\CstoreService;
use App\Services\DepartmentService;
use App\Services\UserProfileService;
use Techup\ApiConfig\Services\LanguageService;
use App\Services\MyDepartmentService;
use App\Services\SendProjectService;
use App\Services\TeamService;
use App\Services\UserConfigService;
use Illuminate\Http\JsonResponse;

class DepartmentController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestIndexMyTrait;

    /**
     * @var MyDepartmentService
     */
    protected $myService;

    /**
     * @var LanguageService
     */
    protected $languageService;
    /**
     * @var CstoreService
     */
    protected $cstoreService;

    /**
     * @param DepartmentService $service
     * @param MyDepartmentService $myService
     * @param LanguageService $languageService
     */
    public function __construct(
        DepartmentService   $service,
        MyDepartmentService $myService,
        LanguageService     $languageService,
        TeamService         $teamService,
        SendProjectService $sendProjectService,
        UserProfileService $userProfileService,
        CstoreService $cstoreService
    )
    {
        $this->service = $service;
        $this->myService = $service;
        $this->languageService = $languageService;
        $this->teamService = $teamService;
        $this->sendProjectService = $sendProjectService;
        $this->userProfileService = $userProfileService;
        $this->resourceCollectionClass = DepartmentResourceCollection::class;
        $this->resourceClass = DepartmentResource::class;
        $this->storeRequest = DepartmentRequest::class;
        $this->editRequest = UpdateDepartmentRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->cstoreService = $cstoreService;
    }

    /**
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store()
    {
        $request = app($this->storeRequest);

        //Allowed language
        if (!$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $model = $this->service->create(array_merge($request->all(), [
            'is_default' => true,
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => $request->get('user_uuid') ?? auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function edit($id)
    {
        $request = app($this->editRequest);

        $model = $this->service->findOrFailById($id);

        //Allowed language
        if ($request->name && !$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => $request->get('user_uuid') ?? auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function indexMy(IndexRequest $request)
    {
        $business = $this->getBusiness();
        $businessUuid = $business ? $business->uuid : null;
        $models = $this->service->getIndexMyWithDefault($request, $businessUuid);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    /**
     * @param MyDepartmentRequest $request
     * @return JsonResponse
     */
    public function storeMyDepartment(MyDepartmentRequest $request)
    {
        //Allowed language
        if (!$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }
        $business = $this->getBusiness();
        if (!$business) {
            return $this->sendJsonResponse(false, 'Does not have business', [], 403);
        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));
        $this->cstoreService->storeFolderByType($model->name, $model->uuid, config('foldertypecstore.DEPARTMENT'), $request->get('location_uuid'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMyDepartment($id)
    {
        $model = $this->service->showMyAndPublicDepartment($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMyDepartmentRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMyDepartment(UpdateMyDepartmentRequest $request, $id)
    {
        $model = $this->service->showMyDepartment($id);

        //Allowed language
        if ($request->name && !$this->languageService->checkLanguages($request->name)) {
            return $this->sendValidationFailedJsonResponse();
        }

        $this->service->update($model, array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyMyDepartment($id, OptionDeleteBusinuessRequest $request)
    {
        $this->service->deleteMyDepartment($id);
        $this->cstoreService->deleteFolderType($id, config('foldertypecstore.DEPARTMENT'),
            $request->get('option', 'keep'));

        return $this->sendOkJsonResponse();
    }


    /**
     * @param AddDepartmentForBusinessRequest $request
     * @return JsonResponse
     */
    public function addDepartmentForBusiness(AddDepartmentForBusinessRequest $request)
    {
        foreach ($request->get('department_uuids') as $departmentUuid) {
            $department = $this->service->findOrFailById($departmentUuid);
            $department->update(['business_uuid' => $request->get('business_uuid')]);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param AddDepartmentForLocationRequest $request
     * @return JsonResponse
     */
    public function addDepartmentForLocation(AddDepartmentForLocationRequest $request)
    {
        foreach ($request->get('department_uuids') as $departmentUuid) {
            $department = $this->service->findOrFailById($departmentUuid);
            $department->update(['location_uuid' => $request->get('location_uuid')]);
            $this->cstoreService->storeFolderByType($department->name, $department->uuid, config('foldertypecstore.DEPARTMENT'), $request->get('location_uuid'));
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param RemoveTeamFromDepartmentRequest $request
     * @return JsonResponse
     */
    public function removeTeam(RemoveTeamFromDepartmentRequest $request)
    {
        foreach ($request->get('team_uuids') as $teamUuid) {
            $team = $this->teamService->findOneWhere(['uuid' => $teamUuid, 'department_uuid' => $request->get('department_uuid')]);
            if ($team) {
                $team->update(['department_uuid' => null]);
            }
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function getAssignableForProject(IndexRequest $request, $id) {
        $sendProject = $this->sendProjectService->findOrFailById($id);
        $locationUuids = $sendProject->business->locations->pluck('uuid');
        $locationUuids = $locationUuids->toArray() ?? [];
        $departments = $this->service->getDepartmentsAssignable($locationUuids, $id, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $departments)
        );
    }

    public function toggleDefaultDepartment($id): JsonResponse
    {
        $model = $this->service->findOrFailById($id);
        $model->update(['status' => !$model->status]);

        return $this->sendOkJsonResponse();
    }

    public function setAppForDepartment(SetAppForDepartmentRequest $request)
    {
        $business = $this->getBusiness();
        if (!$business) {
            return $this->sendJsonResponse(false, __('business.not_business'), [], 403);
        }
        foreach ($request->get('department_uuids') as $departmentUuid) {
            $department = $this->service->findOrFailById($departmentUuid);
            if ($request->get('type', 'assign') === 'assign') {
                $department->app()->syncWithoutDetaching($request->get('app_uuids'), []);
            } else {
                $department->app()->detach($request->get('app_uuids'), []);
            }
        }

        return $this->sendOkJsonResponse();
    }
}
