<?php

namespace App\Http\Controllers\Api;

use App\Abstracts\AbstractRestAPIController;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Requests\AddChildrenProjectRequest;
use App\Http\Requests\AssignProjectForDepartmentRequest;
use App\Http\Requests\AssignProjectForLocationRequest;
use App\Http\Requests\AssignProjectForTeamRequest;
use App\Http\Requests\IndexProjectRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\MySendProjectRequest;
use App\Http\Requests\OptionDeleteBusinuessRequest;
use App\Http\Requests\UpdateMySendProjectRequest;
use App\Http\Requests\UpdateSendProjectRequest;
use App\Http\Requests\VerifyDomainWebsiteVerificationRequest;
use App\Http\Requests\SendProjectRequest;
use App\Http\Requests\WebsiteVerificationRequest;
use App\Http\Resources\SendProjectResourceCollection;
use App\Http\Resources\SendProjectResource;
use App\Http\Resources\WebsiteVerificationResource;
use App\Models\BusinessManagement;
use App\Models\Role;
use App\Services\DepartmentService;
use App\Services\CstoreService;
use App\Services\FileVerificationService;
use App\Services\LocationService;
use App\Services\MySendProjectService;
use App\Services\SendProjectService;
use App\Services\WebsiteVerificationService;
use Illuminate\Http\JsonResponse;

class SendProjectController extends AbstractRestAPIController
{
    use RestIndexTrait, RestShowTrait, RestIndexMyTrait;

    /**
     * @var MySendProjectService
     */
    protected $myService;

    /**
     * @var WebsiteVerificationService
     */
    protected $websiteVerificationService;

    /**
     * @var FileVerificationService
     */
    protected $fileVerificationService;
    /**
     * @var CstoreService
     */
    protected $cstoreService;

    /**
     * @param SendProjectService $service
     * @param WebsiteVerificationService $websiteVerificationService
     * @param FileVerificationService $fileVerificationService
     * @param MySendProjectService $myService
     */
    public function __construct(
        SendProjectService         $service,
        WebsiteVerificationService $websiteVerificationService,
        FileVerificationService    $fileVerificationService,
        MySendProjectService       $myService,
        DepartmentService          $departmentService,
        LocationService            $locationService,
        CstoreService              $cstoreService
    )
    {
        $this->service = $service;
        $this->myService = $service;
        $this->departmentService = $departmentService;
        $this->locationService = $locationService;
        $this->resourceCollectionClass = SendProjectResourceCollection::class;
        $this->resourceClass = SendProjectResource::class;
        $this->storeRequest = SendProjectRequest::class;
        $this->editRequest = UpdateSendProjectRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->websiteVerificationService = $websiteVerificationService;
        $this->fileVerificationService = $fileVerificationService;
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

        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function indexMy(IndexProjectRequest $request)
    {
        if (auth()->hasRole([Role::ROLE_USER_OWNER])) {
            $business = $this->getBusiness();
            if ($business) {
                $models = $this->service->getCollectionWithPaginationByCondition($request, ['business_uuid' => $business->uuid]);
                if ($request->get('type')) {
                    $models = $this->service->getProjectScope($request, $business);
                }
            } else {
                $models = $this->service->getCollectionWithPaginationByCondition($request, ['owner_uuid' => auth()->userId()]);
            }
        } elseif (auth()->hasRole([Role::ROLE_USER_MANAGER])) {
            $department = $this->departmentService->findOneWhere(['manager_uuid' => auth()->userId()]);
            $location = $this->locationService->findOneWhere(['manager_uuid' => auth()->userId()]);
            if ($department) {
                $teamOfDepartment = $department->teams->pluck('uuid')->toArray() ?? [];
                if (!empty($department->location)) {
                    $locationUuid = $department->location->uuid;
                    $businessUuid = $department->location->business_uuid;
                }
                $models = $this->service->getMyProjectWithDepartment($request, $department->uuid, $businessUuid ?? null, $locationUuid ?? null, $teamOfDepartment);
            }

            if ($location) {
                $departmentOfLocation = $location->departments->pluck('uuid')->toArray() ?? [];
                if (!empty($departmentOfLocation)) {
                    $teams = [];
                    foreach ($location->departments as $department) {
                        if (!empty($department->teams->toArray())) {
                            $teams[] = $department->teams->pluck('uuid')->toArray();
                        }
                    }
                }

                $models = $this->service->getMyProjectWithDLocation($request, $location->uuid, $location->business_uuid, $departmentOfLocation, $teams ?? []);
            }
        } else {
            $teams = auth()->user()->teams->pluck('uuid');
            $departments = [];
            $locations = [];
            if ($teams){
                $teams = $teams->toArray() ?? [];
                foreach (auth()->user()->teams as $team) {
                    if (!empty($team->department)) {
                        $department = $team->department;
                        $departments[] = $department->uuid;
                        if (!empty($department->location)) {
                            $location = $department->location;
                            $locations[] = $location->uuid;
                            $business = $location->business_uuid;
                        }
                    }
                }
            }

            $models = $this->service->getMyProjectWithTeams($request, $teams, $departments, $locations, $business ?? null);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
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

        if (empty($request->get('user_uuid')) || $model->user_uuid == $request->get('user_uuid')
            || !$this->service->checkExistsWebisteInTables($id)) {

            $data = $request->all();
        } else {
            return $this->sendValidationFailedJsonResponse(["errors" => ["user_uuid" => __('messages.user_uuid_not_changed')]]);
        }

        $this->service->update($model, array_merge($data, [
            'user_uuid' => $request->get('user_uuid') ?? auth()->userId(),
            'app_id' => auth()->appId(),
            'domain_uuid' => $request->get('domain_uuid') ?? null
        ]));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        if (!$this->service->checkExistsWebisteInTables($id)) {
            $this->service->destroy($id);

            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);

    }

    /**
     * @param MySendProjectRequest $request
     * @return JsonResponse
     */
    public function storeMySendProject(MySendProjectRequest $request)
    {
        $business = $this->getBusiness();
        if (!$business) {
            return $this->sendJsonResponse(false, 'Does not have business', [], 403);
        }
        $model = $this->service->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'business_uuid' => $business->uuid
        ]));

        $this->cstoreService->storeFolderByType($request->get('name'), $model->uuid, BusinessManagement::PROJECT_ENTITY, $business->uuid, BusinessManagement::BUSINESS_ENTITY);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function showMySendProject($id)
    {
        $model = $this->service->showMyWebsite($id);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    /**
     * @param UpdateMySendProjectRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editMySendProject(UpdateMySendProjectRequest $request, $id)
    {
        $model = $this->service->showMyWebsite($id);

        if($request->get('parent_uuid') and $request->get('parent_uuid') != $model->parent_uuid){
            $this->cstoreService->storeFolderByType($model->name, $model->uuid, BusinessManagement::PROJECT_ENTITY, $request->get('parent_uuid'), BusinessManagement::PROJECT_ENTITY);
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
    public function destroyMySendProject($id, OptionDeleteBusinuessRequest $request)
    {
        if (!$this->service->checkExistsWebisteInTables($id)) {
            $this->service->deleteMyWebsite($id);

            $this->cstoreService->deleteFolderType($id, BusinessManagement::PROJECT_ENTITY, $request->get('option', 'keep'));


            return $this->sendOkJsonResponse();
        }

        return $this->sendValidationFailedJsonResponse(["errors" => ["deleted_uuid" => __('messages.data_not_deleted')]]);

    }

    /**
     * @param WebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByDnsRecord(WebsiteVerificationRequest $request)
    {
        $website = $this->service->findOrFailById($request->get('domain_uuid'));
        $websiteVerify = $this->websiteVerificationService->verifyByDnsRecord($website->getKey());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)
        );

    }

    /**
     * @param VerifyDomainWebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByHtmlTag(VerifyDomainWebsiteVerificationRequest $request)
    {
        $website = $this->service->findOrFailById($request->get('domain_uuid'));

        $websiteVerify = $this->websiteVerificationService->verifyByHtmlTag($website->getKey());

        $metaTagName = config('app.name') . '-verify-tag';
        $HtmlTag = "<meta name='" . $metaTagName . "' content='" . $websiteVerify->token . "'>";

        return $this->sendOkJsonResponse([
            'data' => [
                'htmlTag' => $HtmlTag,
                'websiteVerify' => $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)['data']
            ]
        ]);

    }

    /**
     * @param VerifyDomainWebsiteVerificationRequest $request
     * @return JsonResponse
     */
    public function verifyByHtmlFile(VerifyDomainWebsiteVerificationRequest $request)
    {
        $website = $this->service->findOrFailById($request->get('domain_uuid'));

        $websiteVerify = $this->websiteVerificationService->verifyByHtmlFile($website->getKey());

        if ($websiteVerify->verified_at) {

            return $this->sendOkJsonResponse(
                $this->service->resourceToData(WebsiteVerificationResource::class, $websiteVerify)
            );
        } else {

            return $this->sendOkJsonResponse([
                'linkDownloadHtmlFile' => route('website.downloadHtmlFile', [$websiteVerify->token])
            ]);
        }
    }

    /**
     * @param $token
     * @return \Illuminate\Http\Response|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function downloadHtmlFile($token)
    {
        $verificationFileName = $this->fileVerificationService->verificationFileName();
        $contentFile = $token;
        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $verificationFileName),
            'Content-Length' => strlen($contentFile),
        ];

        return response()->make($contentFile, 200, $headers);
    }

    public function assignForTeam(AssignProjectForTeamRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        foreach ($request->get('team_uuids') as $teamUuid){
            $childProject = $this->service->create(array_merge($sendProject->toArray(),
                [
                    'parent_uuid' => $sendProject->uuid,
                    'status' => $request->get('status', $sendProject->status)
                ]
            ));

            $this->cstoreService->storeFolderByType($childProject->name, $childProject->uuid, BusinessManagement::PROJECT_ENTITY, $teamUuid, BusinessManagement::TEAM_ENTITY);

            $childProject->teams()->syncWithoutDetaching([$teamUuid]);
        }
//        $childProject = $this->service->create(array_merge($sendProject->toArray(),
//            [
//                'parent_uuid' => $sendProject->uuid,
//                'status' => $request->get('status', $sendProject->status)
//            ]
//        ));
//        $childProject->teams()->syncWithoutDetaching($request->get('team_uuids', []));

        return $this->sendOkJsonResponse();
    }

    public function unassignForTeam(AssignProjectForTeamRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        $sendProject->teams()->detach($request->get('team_uuids', []));
        $this->cstoreService->storeFolderByType($sendProject->name, $sendProject->uuid, BusinessManagement::PROJECT_ENTITY, $sendProject->business_uuid, BusinessManagement::BUSINESS_ENTITY);


        return $this->sendOkJsonResponse();
    }

    public function assignForDepartment(AssignProjectForDepartmentRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        foreach ($request->get('department_uuids') as $departmentUuid){
            $childProject = $this->service->create(array_merge($sendProject->toArray(),
                [
                    'parent_uuid' => $sendProject->uuid,
                    'status' => $request->get('status', $sendProject->status)
                ]
            ));

            $this->cstoreService->storeFolderByType($childProject->name, $childProject->uuid, BusinessManagement::PROJECT_ENTITY, $departmentUuid, BusinessManagement::DEPARTMENT_ENTITY);

            $childProject->departments()->syncWithoutDetaching([$departmentUuid]);
        }
//        $childProject = $this->service->create(array_merge($sendProject->toArray(),
//            [
//                'parent_uuid' => $sendProject->uuid,
//                'status' => $request->get('status', $sendProject->status)
//            ]
//        ));
//        $childProject->departments()->syncWithoutDetaching($request->get('department_uuids', []));

        return $this->sendOkJsonResponse();
    }

    public function unassignForDepartment(AssignProjectForDepartmentRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        $sendProject->departments()->detach($request->get('department_uuids', []));

        return $this->sendOkJsonResponse();
    }

    public function assignForLocation(AssignProjectForLocationRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        foreach ($request->get('location_uuids') as $locationUuid){
            $childProject = $this->service->create(array_merge($sendProject->toArray(),
                [
                    'parent_uuid' => $sendProject->uuid,
                    'status' => $request->get('status', $sendProject->status)
                ]
            ));

            $this->cstoreService->storeFolderByType($childProject->name, $childProject->uuid, BusinessManagement::PROJECT_ENTITY, $locationUuid, BusinessManagement::LOCATION_ENTITY);

            $childProject->departments()->syncWithoutDetaching([$locationUuid]);
        }
//        $childProject = $this->service->create(array_merge($sendProject->toArray(),
//            [
//                'parent_uuid' => $sendProject->uuid,
//                'status' => $request->get('status', $sendProject->status)
//            ]
//        ));
//        $childProject->locations()->syncWithoutDetaching($request->get('location_uuids', []));

        return $this->sendOkJsonResponse();
    }

    public function unassignForLocation(AssignProjectForLocationRequest $request)
    {
        if (!$this->checkExistBusiness()) {

            return $this->sendJsonResponse(false, 'You do not have access', [], 403);
        }
        $sendProject = $this->service->findOrFailById($request->get('send_project_uuid'));
        $sendProject->locations()->detach($request->get('location_uuids', []));

        return $this->sendOkJsonResponse();
    }

    public function addChildrenForSendProject(AddChildrenProjectRequest $request)
    {
        foreach ($request->get('children_send_project_uuids') as $childProject) {
            $childTeam = $this->service->findOrFailById($childProject);
            $childTeam->update(['parent_uuid' => $request->get('send_project_uuid')]);
            $this->cstoreService->storeFolderByType($childTeam->name, $childTeam->uuid, BusinessManagement::PROJECT_ENTITY, $request->get('send_project_uuid'), BusinessManagement::PROJECT_ENTITY);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function getAssignableForTeam(IndexRequest $request, $id)
    {
        $departments = $this->departmentService->getByTeam($id);
        $locations = $this->locationService->getByTeam($id);
        $locationUuids = $locations->pluck('uuid')->toArray();
        $departmentUuids = $departments->pluck('uuid')->toArray();
        $projects = $this->service->getProjectAssignableForTeam($locationUuids, $departmentUuids, $id, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $projects)
        );
    }
}
