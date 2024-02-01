<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\addChildTeamRequest;
use App\Http\Requests\AddTeamForDepartmentRequest;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\BusinessTeamRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\JoinTeamRequest;
use App\Http\Requests\MyUpdateTeamRequest;
use App\Http\Requests\OptionDeleteBusinuessRequest;
use App\Http\Requests\removeTeamMemberRequest;
use App\Http\Requests\ResetPasswordEmailTeamMemberRequest;
use App\Http\Requests\SetAddOnForMemberRequest;
use App\Http\Requests\SetAddOnTeamMemberRequest;
use App\Http\Requests\SetContactListRequest;
use App\Http\Requests\SetPermissionForTeamRequest;
use App\Http\Requests\MyTeamRequest;
use App\Http\Requests\SetTeamAddOnRequest;
use App\Http\Requests\SetTeamLeaderRequest;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\UpdateBusinessTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\AddOnResource;
use App\Http\Resources\AddOnResourceCollection;
use App\Http\Resources\ContactListResourceCollection;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TeamResourceCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTeamResource;
use App\Http\Resources\UserTeamResourceCollection;
use App\Mail\SendInviteToTeam;
use App\Mail\SendInviteToTeamByAccount;
use App\Models\BusinessManagement;
use App\Models\Email;
use App\Models\Invite;
use App\Models\App;
use App\Models\Role;
use App\Models\Team;
use App\Models\UserBusiness;
use App\Services\AddOnService;
use App\Services\BusinessManagementService;
use App\Services\BusinessTeamService;
use App\Services\ContactListService;
use App\Services\CstoreService;
use App\Services\DepartmentService;
use App\Services\InviteService;
use App\Services\LocationService;
use App\Services\MyTeamService;
use App\Services\PermissionService;
use App\Services\SendProjectService;
use App\Services\SmtpAccountService;
use App\Services\TeamService;
use App\Services\UserBusinessService;
use App\Services\UserProfileService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Techup\ApiBase\Services\UserManagerService;
use Techup\Mailbox\Facades\Mailbox;

class TeamController extends Controller
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    /**
     * @var CstoreService
     */
    protected $cstoreService;

    public function __construct(
        TeamService               $service,
        UserTeamService           $userTeamService,
        SmtpAccountService        $smtpAccountService,
        UserService               $userService,
        BusinessManagementService $businessManagementService,
        InviteService             $inviteService,
        PermissionService         $permissionService,
        ContactListService        $contactListService,
        MyTeamService             $myService,
        UserBusinessService       $userBusinessService,
        AddOnService              $addOnService,
        DepartmentService $departmentService,
        LocationService $locationService,
        SendProjectService $sendProjectService,
        CstoreService $cstoreService,
        UserProfileService $userProfileService
    )
    {
        $this->service = $service;
        $this->myService = $service;
        $this->smtpAccountService = $smtpAccountService;
        $this->userTeamService = $userTeamService;
        $this->userService = $userService;
        $this->userProfileService = $userProfileService;
        $this->inviteService = $inviteService;
        $this->businessManagementService = $businessManagementService;
        $this->permissionService = $permissionService;
        $this->contactListService = $contactListService;
        $this->userBusinessService = $userBusinessService;
        $this->addOnService = $addOnService;
        $this->departmentService = $departmentService;
        $this->locationService = $locationService;
        $this->sendProjectService = $sendProjectService;
        $this->resourceCollectionClass = TeamResourceCollection::class;
        $this->addOnResourceCollectionClass = AddOnResourceCollection::class;
        $this->userTeamResourceClass = UserTeamResource::class;
        $this->contactListresourceCollectionClass = ContactListResourceCollection::class;
        $this->userTeamResourceCollectionClass = UserTeamResourceCollection::class;
        $this->resourceClass = TeamResource::class;
        $this->userResourceClass = UserResource::class;
        $this->storeRequest = TeamRequest::class;
        $this->editRequest = UpdateTeamRequest::class;
        $this->indexRequest = IndexRequest::class;
        $this->cstoreService = $cstoreService;
    }

    public function index(IndexRequest $request)
    {
        if ($request->get('sort') == 'num_of_team_member' || $request->get('sort') == '-num_of_team_member') {
            $models = $this->service->sortByNumOfTeamMember($request);
        } else {
            $models = $this->service->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function indexMy(IndexRequest $request)
    {
        if ($request->get('sort') == 'num_of_team_member' || $request->get('sort') == '-num_of_team_member') {
            $models = $this->service->sortByNumOfTeamMemberForMy($request);
        } else {
            $models = $this->service->getCollectionWithPaginationByCondition($request, ['owner_uuid' => auth()->userId()]);
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showMy($id)
    {
        $model = $this->service->findOneWhereOrFail(['uuid' => $id, 'owner_uuid' => auth()->userId(), 'app_id' => auth()->appId()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storeMy(MyTeamRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        if($request->get('parent_team_uuid')){
            $parentUuid = $request->get('parent_team_uuid');
            $parentType = BusinessManagement::TEAM_ENTITY;
        }else{
            $parentUuid = $request->get('department_uuid');
            $parentType = BusinessManagement::DEPARTMENT_ENTITY;
        }
        $this->cstoreService->storeFolderByType($model->name, $model->uuid, BusinessManagement::TEAM_ENTITY, $parentUuid, $parentType);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function inviteUser(InviteUserRequest $request)
    {
        DB::beginTransaction();
        try{
            $invite = $this->inviteService->create(array_merge($request->all(), [
                'status' => Invite::NEW_STATUS
            ]));
            if ($request->get('type') == Team::LINK_INVITE) {
                $url = env('FRONTEND_URL') . 'auth/register?invite_uuid=' . $invite->uuid;
                $this->smtpAccountService->sendEmailNotificationSystem(null, new SendInviteToTeam($invite, $url), $request->get('email'));
            } elseif ($request->get('type') == Team::ACCOUNT_INVITE) {
                $password = $this->generateRandomString(10);
                $addUser = app(UserManagerService::class)->addUser($request->get('email'), $password, $request->get('first_name'), $request->get('last_name'), auth()->appId());
                if ($addUser) {
                    $userProfile = $this->userProfileService->findOneWhereOrFail(['email' => $request->get('email')]);
                    $userProfile->userApp()->create(['platform_package_uuid' => App::DEFAULT_PLATFORM_PACKAGE_1, 'app_id' => $userProfile->app_id]);

                    $this->userTeamService->create(array_merge($request->all(), [
                        'user_uuid' => $userProfile->user_uuid,
                        'app_id' => auth()->appId()
                    ]));
                    $this->cstoreService->storeFolderByType($request->get('email'), $userProfile->user_uuid, BusinessManagement::USER_ENTITY, $request->get('team_uuid'));
                    $this->smtpAccountService->sendEmailNotificationSystem($userProfile, new SendInviteToTeamByAccount($userProfile, $password));
//              Mailbox::postEmailAccountcreate($user->user_uuid, $email, $password);
                }
                DB::commit();
            }
            return $this->sendCreatedJsonResponse();
        } catch (ConnectionException $exception) {
            DB::rollBack();
            return $this->sendInternalServerErrorJsonResponse();
        }

    }

    /**
     * @param AddTeamMemberRequest $request
     * @return JsonResponse
     */
    public function addTeamMember(AddTeamMemberRequest $request)
    {
        DB::beginTransaction();
        try {
            //get business uuid
            if (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
                $businessUuid = $request->get("business_uuid");
            } else {
                $businesses = $this->businessManagementService->findAllWhere([['owner_uuid', auth()->userId()], ['app_id', auth()->appId()]]);
                if ($businesses->toArray()) {
                    $businessUuid = $businesses->first()->uuid;
                } else {

                    return $this->sendJsonResponse(false, __('business.not_business'), [], 403);
                }
            }
            if ($request->get('type') == Team::ACCOUNT_INVITE) {
//                $password = Hash::make($request->get('password'));
                $password = $request->get('password');
                $email = $request->get('email') . '@' . $request->get('domain');
                $addUser = app(UserManagerService::class)->addUser($email, $password, $request->get('first_name'), $request->get('last_name'), auth()->appId());
                if ($addUser) {
                    $userProfile = $this->userProfileService->findOneWhereOrFail(['email' => $email]);
                    $userProfile->userApp()->create(['platform_package_uuid' => App::DEFAULT_PLATFORM_PACKAGE_1, 'app_id' => $userProfile->app_id]);

                    $this->userTeamService->create(array_merge($request->all(), [
                        'user_uuid' => $userProfile->user_uuid,
                        'app_id' => auth()->appId()
                    ]));

                    $this->userBusinessService->create([
                        'business_uuid' => $businessUuid,
                        'user_uuid' => $userProfile->user_uuid,
                        'app_id' => auth()->appId()
                    ]);
                    $this->cstoreService->storeFolderByType($userProfile->email, $userProfile->user_uuid, BusinessManagement::USER_ENTITY, $businessUuid);

//                    Mailbox::postEmailAccountcreate($userProfile->user_uuid, $email, $password);
                }


                DB::commit();

                return $this->sendCreatedJsonResponse();
            } elseif ($request->get('type') == Team::ALREADY_EXISTS_ACCOUNT) {
                foreach ($request->get('user_uuids') as $userUuid) {
                    $existingRecord = $this->userTeamService->findOneWhere([
                        'team_uuid' => $request->get('team_uuid'),
                        'user_uuid' => $userUuid,
                        'app_id' => auth()->appId()
                    ]);

                    if (!$existingRecord) {
                        $this->userTeamService->create([
                            'team_uuid' => $request->get('team_uuid'),
                            'user_uuid' => $userUuid,
                            'app_id' => auth()->appId()
                        ]);

                        $userBusiness = $this->userBusinessService->findOneWhere([
                            'business_uuid' => $businessUuid,
                            'user_uuid' => $userUuid,
                            'app_id' => auth()->appId()
                        ]);
                        if (!$userBusiness) {
                            $this->userBusinessService->create([
                                'business_uuid' => $businessUuid,
                                'user_uuid' => $userUuid,
                                'app_id' => auth()->appId()
                            ]);

                            $userProfile = $this->userProfileService->findOneWhere([
                                ['user_uuid', $userUuid],
                                ['app_id', auth()->appId()]
                            ]);
                            $this->cstoreService->storeFolderByType($userProfile->email, $userUuid, BusinessManagement::USER_ENTITY,$businessUuid);
                        }

                    }
                }
                DB::commit();
                return $this->sendCreatedJsonResponse();
            }

            return $this->sendValidationFailedJsonResponse();
        } catch (ConnectionException $exception) {
            DB::rollback();
            return $this->sendInternalServerErrorJsonResponse();
        }
    }

    public function joinTeam(JoinTeamRequest $request)
    {
        $model = $this->userTeamService->create(array_merge($request->all(), [
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceClass, $model)
        );
    }

    public function setPermissionForTeam(SetPermissionForTeamRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $model = $this->userTeamService->findOneWhere([
            'user_uuid' => $request->get('user_uuid'),
            'team_uuid' => $request->get('team_uuid'),
            'app_id' => auth()->appId()
        ]);
        $this->removeTeamPermissionCache($request->get('user_uuid'));
        if (empty($model)) {

            return $this->sendBadRequestJsonResponse(['message' => 'This user is not in the team']);
        }
        $this->userTeamService->update($model, [
            'permission_uuids' => $request->get('permission_uuids'),
        ]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceClass, $model)
        );
    }

    public function setAddOnsMembers(SetAddOnForMemberRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }

        foreach ($request->get('user_uuids') as $userUuid) {
            $userTeam = $this->userTeamService->findOneWhereOrFail([
                'team_uuid' => $request->get('team_uuid'),
                'user_uuid' => $userUuid,
                'app_id' => auth()->appId()
            ]);
            $userTeam->addOns()->syncWithoutDetaching($request->get('add_on_uuids'), []);
            $this->removeCache($userUuid);
        }
        $this->removeTeamAddOnPermissionCache($request->get('user_uuids'));

        return $this->sendOkJsonResponse();
    }

    public function unsetAddOnsMembers(SetAddOnForMemberRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }

        foreach ($request->get('user_uuids') as $userUuid) {
            $userTeam = $this->userTeamService->findOneWhereOrFail([
                'team_uuid' => $request->get('team_uuid'),
                'user_uuid' => $userUuid,
                'app_id' => auth()->appId()
            ]);
            $userTeam->addOns()->detach($request->get('add_on_uuids'), []);
            $this->removeCache($userUuid);
        }
        $this->removeTeamAddOnPermissionCache($request->get('user_uuids'));

        return $this->sendOkJsonResponse();
    }

    /**
     * @param SetContactListRequest $request
     * @return JsonResponse
     */
    public function setContactList(SetContactListRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => $request->get('user_uuid')]);
        $model = $this->userTeamService->findOneWhere([
            'user_uuid' => $request->get('user_uuid'),
            'team_uuid' => $request->get('team_uuid'),
            'app_id' => auth()->appId()
        ]);
        if (empty($model)) {
            //need function userteamcontaclist here
            return $this->sendBadRequestJsonResponse(['message' => 'This user is not in the team']);
        }
        $user->userTeamContactLists()->syncWithPivotValues($request->get('contact_list_uuids', []), ['app_id' => auth()->appId()]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userResourceClass, $user)
        );
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function listMember(IndexRequest $request, $id)
    {
        $model = $this->userTeamService->listTeamMember([$id], $request);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceCollectionClass, $model)
        );
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function listMemberOfAllTeam(IndexRequest $request)
    {
        if (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            $model = $this->userTeamService->listTeamMemberOfAllTeam($request);
        } else {
            $team_uuids = $this->service->findAllWhere(['owner_uuid' => auth()->userId(), 'app_id' => auth()->appId()])->pluck('uuid');
            $userTeam = $this->userTeamService->getUserTeamByUserAndAppId(auth()->userId(), auth()->appId());
            if ($userTeam) {
                $model = $this->userTeamService->listTeamMember([$userTeam->team_uuid], $request);
            } elseif ($team_uuids) {
                $model = $this->userTeamService->listTeamMember($team_uuids, $request);
            } else {
                $model = [];
            }
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceCollectionClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function permissionOfTeams($id)
    {
        if (!$this->checkTeamOwner($id)) {

            return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $permissions = $this->permissionService->getPermissionOfTeam($team->owner);

        return $this->sendOkJsonResponse(['data' => $permissions]);
    }

    public function getPermissionOfUser($id)
    {
        $permissions = $this->permissionService->getPermissionOfUser($id);

        return $this->sendOkJsonResponse(['data' => $permissions]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function contactListOfTeams($id)
    {
        if (!$this->checkTeamOwner($id)) {

            return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $contactLists = $this->contactListService->findAllWhere([
            'user_uuid' => $team->owner->uuid,
            'app_id' => auth()->appId(),
        ]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->contactListresourceCollectionClass, $contactLists)
        );
    }

    public function editMy(MyUpdateTeamRequest $request, $id)
    {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'uuid' => $id
        ]);

        if($request->get('department_uuid') and $request->get('department_uuid') != $model->department_uuid){
            $parentUuid = $request->get('department_uuid');
            $parentType = BusinessManagement::DEPARTMENT_ENTITY;
            $this->cstoreService->storeFolderByType($model->name, $model->uuid, BusinessManagement::TEAM_ENTITY, $parentUuid, $parentType);
        }

        if($request->get('parent_team_uuid') and $request->get('parent_team_uuid') != $model->parent_team_uuid){
            $parentUuid = $request->get('parent_team_uuid');
            $parentType = BusinessManagement::TEAM_ENTITY;
            $this->cstoreService->storeFolderByType($model->name, $model->uuid, BusinessManagement::TEAM_ENTITY, $parentUuid, $parentType);
        }

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function destroyMy($id, OptionDeleteBusinuessRequest $request)
    {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'uuid' => $id
        ]);

        $this->destroy($model->uuid);
        $this->cstoreService->deleteFolderType($id, BusinessManagement::TEAM_ENTITY,
            $request->get('option', 'keep'));

        return $this->sendOkJsonResponse();
    }

    public function deleteMember(RemoveTeamMemberRequest $request, $id)
    {
        $model = $this->userTeamService->findOneWhereOrFail([
            'user_uuid' => $id,
            'team_uuid' => $request->get('team_uuid'),
            'app_id' => auth()->appId()
        ]);

        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => $id]);
        //need function userteamcontaclist here
        $user->userTeamContactLists()->detach();
        $this->userTeamService->destroy($model->uuid);
        $this->removeTeamPermissionCache($model->user_uuid);
        $this->cstoreService->deleteFolderType($model->user_uuid, BusinessManagement::USER_ENTITY,
            $request->get('option', 'destroy'));

        return $this->sendOkJsonResponse();
    }

    public function blockMember($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($model->team_uuid)) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $this->userTeamService->update($model, ['is_blocked' => true]);

        return $this->sendOkJsonResponse();
    }

    public function unBlockMember($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($model->team_uuid)) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $this->userTeamService->update($model, ['is_blocked' => false]);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param ResetPasswordEmailTeamMemberRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordEmailTeamMemberRequest $request)
    {
        $user = $this->userProfileService->findOneWhereOrFail(['user_uuid' => $request->user_uuid]);

        if ($user) {
            $user->update([
                'password' => Hash::make($request->get('password'))
            ]);

            return $this->sendOkJsonResponse(['message' => __('messages.change_password_success')]);
        }

        return $this->sendValidationFailedJsonResponse();
    }

    public function storeBusinessTeam(BusinessTeamRequest $request)
    {
        $department = $this->departmentService->findOrFailById($request->get('department_uuid'));
        $location = $this->locationService->findOrFailById($department->uuid);
        $teamModel = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => auth()->userId(),
            'location_uuid' => $location->uuid,
            'app_id' => auth()->appId(),
        ]));
        if (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businesses = $this->getBusiness();
            if (!$businesses) {

                return $this->sendJsonResponse(false, __('business.not_business'), [], 403);
            }
            $businessUuid = $businesses->uuid;
        }
        //add team member with user uuid
        $teamModel->business()->attach([$businessUuid]);
        if ($request->get('team_member_uuids')) {
            foreach ($request->get('team_member_uuids') as $userUuid) {
                $existingRecord = $this->userTeamService->findOneWhere([
                    'team_uuid' => $teamModel->uuid,
                    'user_uuid' => $userUuid,
                    'app_id' => auth()->appId()
                ]);

                if (!$existingRecord) {
                    $this->userTeamService->create([
                        'team_uuid' => $teamModel->uuid,
                        'user_uuid' => $userUuid,
                        'app_id' => auth()->appId()
                    ]);
                }
            }
        }

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $teamModel)
        );
    }

    /**
     * @param UpdateBusinessTeamRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function editBusinessTeam(UpdateBusinessTeamRequest $request, $id)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($id)) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $department = $this->departmentService->findOrFailById($request->get('department_uuid'));
        $location = $this->locationService->findOrFailById($department->uuid);
        $teamModel = $this->myService->findOrFailById($id);
        $this->service->update($teamModel, array_merge($request->all(), [
            'location_uuid' => $location->uuid,
        ]));

        $teamMemberUuids = $request->get('team_member_uuids');
        $appId = auth()->appId();
        $syncData = collect($teamMemberUuids)->mapWithKeys(function ($userUuid) use ($appId) {
            return [$userUuid => ['app_id' => $appId]];
        })->toArray();
        $teamModel->users()->syncWithoutDetaching($syncData);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $teamModel)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyBusinessTeam($id, OptionDeleteBusinuessRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($id)) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $model = $this->myService->findOneWhereOrFail([
            'uuid' => $id
        ]);
        $this->destroy($model->uuid);
        $this->cstoreService->deleteFolderType($id, BusinessManagement::TEAM_ENTITY,
            $request->get('option', 'keep'));

        return $this->sendOkJsonResponse();
    }

    public function setTeamLeader(SetTeamLeaderRequest $request)
    {
        DB::beginTransaction();
        try{
            $team = $this->service->findOrFailById($request->get('team_uuid'));
            $this->removeTeamLeaderPermissionCache($team->leader_uuid);
            $this->service->update($team, ['leader_uuid' => $request->get('team_member_uuid')]);
            $this->removeTeamLeaderPermissionCache($request->get('team_member_uuid'));
//            $setRole = app(UserManagerService::class)->addRoleToUser($request->get('team_member_uuid'), Role::ROLE_USER_LEADER, auth()->appId(), auth()->userId(), auth()->token());
//            if (!$setRole) {
//                DB::rollBack();
//                return $this->sendInternalServerErrorJsonResponse();
//            }
        DB::commit();
            return $this->sendOkJsonResponse(
                $this->service->resourceToData($this->resourceClass, $team)
            );
        } catch (ConnectionException $exception) {
            DB::rollBack();
            return $this->sendInternalServerErrorJsonResponse();
        }
    }

    /**
     * @param SetTeamAddOnRequest $request
     * @return JsonResponse
     */
    public function setAddOnForTeam(SetTeamAddOnRequest $request)
    {
        $team = $this->service->findOrFailById($request->get('team_uuid'));
        $team->addons()->syncWithoutDetaching($request->get('add_on_uuids', []));
        $this->removeTeamLeaderPermissionCache($team->leader_uuid);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $team)
        );
    }

    public function unsetAddOnForTeam(SetTeamAddOnRequest $request)
    {
        if (!auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, __('business.check_permission'), [], 403);
            }
        }
        $team = $this->service->findOrFailById($request->get('team_uuid'));
        $team->addons()->detach($request->get('add_on_uuids', []));
        $this->removeTeamLeaderPermissionCache($team->leader_uuid);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $team)
        );
    }

    /**
     * @param addChildTeamRequest $request
     * @return JsonResponse
     */
    public function addChildrenForTeam(addChildTeamRequest $request)
    {
        foreach ($request->get('child_team_uuids') as $childTeamUuid) {
            $childTeam = $this->service->findOrFailById($childTeamUuid);
            $childTeam->update(['parent_team_uuid' => $request->get('team_uuid')]);
            $this->cstoreService->storeFolderByType(
                $childTeam->name, $childTeam->uuid, BusinessManagement::TEAM_ENTITY,
                $request->get('team_uuid'), BusinessManagement::TEAM_ENTITY);
        }

        return $this->sendOkJsonResponse();
    }

    public function getAddOnOfTeam(IndexRequest $request, $id)
    {
        $addOns = $this->addOnService->getAddOnsByTeam($request, $id);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->addOnResourceCollectionClass, $addOns)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function businessTeam(IndexRequest $request, $id)
    {
        $team = $this->service->findOrFailById($id);
        $childrenTeam = $this->service->getCollectionWithPaginationByCondition($request, ['parent_team_uuid' => $id]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $childrenTeam)
        );
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function assignedBusinessTeam(IndexRequest $request, $id)
    {
        $addOn = $this->addOnService->findOrFailById($id);
        $teamUuids = $addOn->teams()->pluck('uuid')->toArray();
        $teams = $this->service->getTeamsByIds($teamUuids, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $teams)
        );
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function assignedTeamMember(IndexRequest $request, $id)
    {
        $addOn = $this->addOnService->findOrFailById($id);
        $userTeamUuids = $addOn->userTeams->pluck('uuid')->toArray();
        $userTeams = $this->userTeamService->getUserTeamsByIds($userTeamUuids, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->userTeamResourceCollectionClass, $userTeams)
        );
    }


    /**
     * @param AddTeamForDepartmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeamForDepartment(AddTeamForDepartmentRequest $request)
    {
        foreach ($request->get('team_uuids') as $childTeamUuid) {
            $childTeam = $this->service->findOrFailById($childTeamUuid);
            $childTeam->update(['department_uuid' => $request->get('department_uuid')]);
            $this->cstoreService->storeFolderByType($childTeam->name, $childTeam->uuid, BusinessManagement::TEAM_ENTITY, $request->get('department_uuid'), BusinessManagement::DEPARTMENT_ENTITY);
        }

        return $this->sendOkJsonResponse();
    }

    /**
     * @param IndexRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function getAssignableForProject(IndexRequest $request, $id) {
        $departments = $this->departmentService->getByProject($id);
        $departmentUuids = $departments->pluck('uuid')->toArray();
        $teams = $this->service->getTeamsAssignable($departmentUuids, $id, $request);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $teams)
        );
    }
}
