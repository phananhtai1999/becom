<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\BusinessTeamRequest;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\JoinTeamRequest;
use App\Http\Requests\MyUpdateTeamRequest;
use App\Http\Requests\ResetPasswordEmailTeamMemberRequest;
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
use App\Models\Email;
use App\Models\Invite;
use App\Models\PlatformPackage;
use App\Models\Role;
use App\Models\Team;
use App\Services\AddOnService;
use App\Services\BusinessTeamService;
use App\Services\ContactListService;
use App\Services\InviteService;
use App\Services\MyTeamService;
use App\Services\PermissionService;
use App\Services\SmtpAccountService;
use App\Services\TeamService;
use App\Services\UserBusinessService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Techup\Mailbox\Facades\Mailbox;

class TeamController extends Controller
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(
        TeamService        $service,
        UserTeamService    $userTeamService,
        SmtpAccountService $smtpAccountService,
        UserService        $userService,
        InviteService      $inviteService,
        PermissionService  $permissionService,
        ContactListService $contactListService,
        MyTeamService      $myService,
        UserBusinessService $userBusinessService,
        AddOnService $addOnService
    )
    {
        $this->service = $service;
        $this->myService = $myService;
        $this->smtpAccountService = $smtpAccountService;
        $this->userTeamService = $userTeamService;
        $this->userService = $userService;
        $this->inviteService = $inviteService;
        $this->permissionService = $permissionService;
        $this->contactListService = $contactListService;
        $this->userBusinessService = $userBusinessService;
        $this->addOnService = $addOnService;
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
            $models = $this->myService->sortByNumOfTeamMember($request);
        } else {
            $models = $this->myService->getCollectionWithPagination();
        }

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->resourceCollectionClass, $models)
        );
    }

    public function showMy($id)
    {
        $model = $this->service->findOneWhereOrFail(['uuid' => $id, 'owner_uuid' => $this->user()->getKey()]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function storeMy(MyTeamRequest $request)
    {
        $model = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function inviteUser(InviteUserRequest $request)
    {
        $invite = $this->inviteService->create(array_merge($request->all(), [
            'status' => Invite::NEW_STATUS
        ]));
        if ($request->get('type') == Team::LINK_INVITE) {
            $url = env('FRONTEND_URL') . 'auth/register?invite_uuid=' . $invite->uuid;
            $this->smtpAccountService->sendEmailNotificationSystem(null, new SendInviteToTeam($invite, $url), $request->get('email'));
        } elseif ($request->get('type') == Team::ACCOUNT_INVITE) {
            $password = $this->generateRandomString(10);
            $user = $this->userService->create([
                'email' => $request->get('email'),
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'username' => $request->get('email'),
                'can_add_smtp_account' => 0,
                'password' => Hash::make($password)
            ]);
            $user->roles()->attach([config('user.default_role_uuid')]);
            $user->userPlatformPackage()->create(['platform_package_uuid' => PlatformPackage::DEFAULT_PLATFORM_PACKAGE_1]);
            $this->userTeamService->create(array_merge($request->all(), [
                'user_uuid' => $user->uuid,
            ]));
            $this->smtpAccountService->sendEmailNotificationSystem($user, new SendInviteToTeamByAccount($user, $password));
        }

        return $this->sendCreatedJsonResponse();
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
            if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
                $businessUuid = $request->get("business_uuid");
            } else {
                $businesses= $this->user()->businessManagements;
                if (!empty($businesses)) {
                    $businessUuid = $businesses->first()->uuid;
                }
            }
            if ($request->get('type') == Team::ACCOUNT_INVITE) {
                $passwordRandom = $this->generateRandomString(10);
                $email = $request->get('username') . '@' . $request->get('domain');
                $user = $this->userService->create([
                    'email' => $email,
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'username' => $request->get('username'),
                    'can_add_smtp_account' => 0,
                    'password' => Hash::make($request->get('password'))
                ]);
                $user->roles()->attach([config('user.default_role_uuid')]);
                $user->userPlatformPackage()->create(['platform_package_uuid' => PlatformPackage::DEFAULT_PLATFORM_PACKAGE_1]);
                $this->userTeamService->create(array_merge($request->all(), [
                    'user_uuid' => $user->uuid,
                ]));

                $this->userBusinessService->create([
                    'business_uuid' => $businessUuid,
                    'user_uuid' => $user->uuid
                ]);
                Mailbox::postEmailAccountcreate($user->uuid, $email, $passwordRandom);
                DB::commit();

                return $this->sendCreatedJsonResponse();
            } elseif ($request->get('type') == Team::ALREADY_EXISTS_ACCOUNT) {
                foreach ($request->get('user_uuids') as $userUuid) {
                    $existingRecord = $this->userTeamService->findOneWhere([
                        'team_uuid' => $request->get('team_uuid'),
                        'user_uuid' => $userUuid
                    ]);

                    if (!$existingRecord) {
                        $this->userTeamService->create([
                            'team_uuid' => $request->get('team_uuid'),
                            'user_uuid' => $userUuid
                        ]);

                        $userBusiness = $this->userBusinessService->findOneWhere([
                            'business_uuid' => $businessUuid,
                            'user_uuid' => $userUuid
                        ]);
                        if (!$userBusiness) {
                            $this->userBusinessService->create([
                                'business_uuid' => $businessUuid,
                                'user_uuid' => $userUuid
                            ]);
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
            'user_uuid' => auth()->user()->getkey(),
        ]));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceClass, $model)
        );
    }

    public function setPermissionForTeam(SetPermissionForTeamRequest $request)
    {
        if ($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
            }
        }
        $model = $this->userTeamService->findOneWhere([
            'user_uuid' => $request->get('user_uuid'),
            'team_uuid' => $request->get('team_uuid')
        ]);
        Cache::forget('team_permission_' . $request->get('user_uuid'));
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

    /**
     * @param SetContactListRequest $request
     * @return JsonResponse
     */
    public function setContactList(SetContactListRequest $request)
    {
        if ($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()) {
            if (!$this->checkTeamOwner($request->get('team_uuid'))) {

                return $this->sendJsonResponse(false, 'You are not owner of team to set contact list', [], 403);
            }
        }
        $user = $this->userService->findOrFailById($request->get('user_uuid'));
        $model = $this->userTeamService->findOneWhere([
            'user_uuid' => $request->get('user_uuid'),
            'team_uuid' => $request->get('team_uuid')
        ]);
        if (empty($model)) {

            return $this->sendBadRequestJsonResponse(['message' => 'This user is not in the team']);
        }

        $user->userTeamContactLists()->sync($request->get('contact_list_uuids', []));

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
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ADMIN, Role::ROLE_ROOT])->first()) {
            $model = $this->userTeamService->listTeamMemberOfAllTeam($request);
        } else {
            $team_uuids = $this->service->findAllWhere(['owner_uuid' => $this->user()->getKey()])->pluck('uuid');
            if ($this->user()->userTeam) {
                $model = $this->userTeamService->listTeamMember([$this->user()->userTeam->team_uuid], $request);
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

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $permisions = $this->permissionService->getPermissionOfTeam($team->owner);

        return $this->sendOkJsonResponse(['data' => $permisions]);
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

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $contactLists = $this->contactListService->findAllWhere(['user_uuid' => $team->owner->uuid]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->contactListresourceCollectionClass, $contactLists)
        );
    }

    public function editMy(MyUpdateTeamRequest $request, $id)
    {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->user()->getKey(),
            'uuid' => $id
        ]);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }

    public function destroyMy($id)
    {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->user()->getKey(),
            'uuid' => $id
        ]);

        $this->destroy($model->uuid);

        return $this->sendOkJsonResponse();
    }

    public function deleteMember($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        if ($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()) {
            if (!$this->checkTeamOwner($model->team_uuid)) {

                return $this->sendJsonResponse(false, 'You are not owner of team to delete member', [], 403);
            }
        }
        $model->user->userTeamContactLists()->detach();
        $this->userTeamService->destroy($model->uuid);
        Cache::forget('team_permission_' . $model->user_uuid);

        return $this->sendOkJsonResponse();
    }

    public function blockMember($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        if ($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()) {
            if (!$this->checkTeamOwner($model->team_uuid)) {

                return $this->sendJsonResponse(false, 'You are not owner of team to block member', [], 403);
            }
        }
        $this->userTeamService->update($model, ['is_blocked' => true]);

        return $this->sendOkJsonResponse();
    }

    public function unBlockMember($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        if ($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()) {
            if (!$this->checkTeamOwner($model->team_uuid)) {

                return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
            }
        }
        $this->userTeamService->update($model, ['is_blocked' => false]);

        return $this->sendOkJsonResponse();
    }

    public function blockMemberForAdmin($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        $this->userTeamService->update($model, ['is_blocked' => true]);

        return $this->sendOkJsonResponse();
    }

    public function unBlockMemberForAdmin($id)
    {
        $model = $this->userTeamService->findOrFailById($id);
        $this->userTeamService->update($model, ['is_blocked' => false]);

        return $this->sendOkJsonResponse();
    }

    /**
     * @param ResetPasswordEmailTeamMemberRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordEmailTeamMemberRequest $request)
    {
        $user = $this->userService
            ->findOneWhere(['uuid' => $request->user_uuid]);

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
        $teamModel = $this->service->create(array_merge($request->all(), [
            'owner_uuid' => $this->user()->getKey(),
        ]));
        if ($this->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $businessUuid = $request->get("business_uuid");
        } else {
            $businessUuid = $this->user()->businessManagements->first()->uuid;
        }
        //add team member with user uuid
        $teamModel->business()->attach([$businessUuid]);
        if ($request->get('team_member_uuids')) {
            foreach ($request->get('team_member_uuids') as $userUuid) {
                $existingRecord = $this->userTeamService->findOneWhere([
                    'team_uuid' => $teamModel->uuid,
                    'user_uuid' => $userUuid
                ]);

                if (!$existingRecord) {
                    $this->userTeamService->create([
                        'team_uuid' => $teamModel->uuid,
                        'user_uuid' => $userUuid
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
        if ($this->user()->roles->whereNotIn('slug', [Role::ROLE_ADMIN, Role::ROLE_ROOT])->count()) {
            if (!$this->checkTeamOwner($id)) {

                return $this->sendJsonResponse(false, 'You are not owner of team to edit', [], 403);
            }
        }
        $teamModel = $this->myService->findOrFailById($id);
        $this->service->update($teamModel, $request->all());
        $teamModel->users()->sync($request->get('team_member_uuids'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->resourceClass, $teamModel)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroyBusinessTeam($id)
    {
        if ($this->user()->roles->whereNotIn('slug', [Role::ROLE_ADMIN, Role::ROLE_ROOT])->count()) {
            if (!$this->checkTeamOwner($id)) {

                return $this->sendJsonResponse(false, 'You are not owner of team to edit', [], 403);
            }
        }
        $model = $this->myService->findOneWhereOrFail([
            'uuid' => $id
        ]);
        $this->destroy($model->uuid);

        return $this->sendOkJsonResponse();
    }

    public function setTeamLeader(SetTeamLeaderRequest $request)
    {
        $team = $this->service->findOrFailById($request->get('team_uuid'));
        $team->update(['leader_uuid' => $request->get('team_member_uuid')]);

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $team)
        );
    }

    /**
     * @param SetTeamAddOnRequest $request
     * @return JsonResponse
     */
    public function setAddOnForTeam(SetTeamAddOnRequest $request)
    {
        $team = $this->service->findOrFailById($request->get('team_uuid'));
        $team->addons()->sync($request->get('add_on_uuids', []));

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $team)
        );
    }

    public function getAddOnOfTeam(IndexRequest $request, $id)
    {
        $addOns = $this->addOnService->getAddOnsByTeam($request, $id);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->addOnResourceCollectionClass, $addOns)
        );
    }
}
