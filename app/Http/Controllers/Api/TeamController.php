<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexMyTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\JoinTeamRequest;
use App\Http\Requests\MyUpdateTeamRequest;
use App\Http\Requests\SetContactListRequest;
use App\Http\Requests\SetPermissionForTeamRequest;
use App\Http\Requests\MyTeamRequest;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\ContactListResource;
use App\Http\Resources\ContactListResourceCollection;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TeamResourceCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserTeamResource;
use App\Http\Resources\UserTeamResourceCollection;
use App\Mail\SendInviteToTeam;
use App\Mail\SendInviteToTeamByAccount;
use App\Models\Invite;
use App\Models\PlatformPackage;
use App\Models\Team;
use App\Services\ContactListService;
use App\Services\InviteService;
use App\Services\MyTeamService;
use App\Services\PermissionService;
use App\Services\SmtpAccountService;
use App\Services\TeamService;
use App\Services\UserContactListService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    use RestShowTrait, RestDestroyTrait, RestEditTrait, RestStoreTrait;

    public function __construct(
        TeamService        $service,
        UserTeamService    $userTeamService,
        SmtpAccountService $smtpAccountService,
        UserService        $userService,
        InviteService      $inviteService,
        PermissionService $permissionService,
        ContactListService $contactListService,
        MyTeamService $myService
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
        $this->resourceCollectionClass = TeamResourceCollection::class;
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
        $invite = $this->inviteService->create([
            'email' => $request->get('email'),
            'team_uuid' => $request->get('team_uuid'),
            'status' => Invite::NEW_STATUS
        ]);
        if ($request->get('type') == Team::LINK_INVITE) {
            $url = env('FRONTEND_URL') . 'auth/register?invite_uuid=' . $invite->uuid;
            $this->smtpAccountService->sendEmailNotificationSystem(null, new SendInviteToTeam($invite, $url), $request->get('email'));
        } elseif ($request->get('type') == Team::ACCOUNT_INVITE) {
            $password = $this->generateRandomString(6);
            $user = $this->userService->create([
                'email' => $request->get('email'),
                'username' => $request->get('email'),
                'can_add_smtp_account' => 0,
                'password' => Hash::make($password)
            ]);;
            $user->roles()->attach([config('user.default_role_uuid')]);
            $this->userTeamService->create(array_merge($request->all(), [
                'user_uuid' => $user->uuid,
            ]));
            $this->smtpAccountService->sendEmailNotificationSystem($user, new SendInviteToTeamByAccount($user, $password));
        }

        return $this->sendCreatedJsonResponse();
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
        if (!$this->checkTeamOwner($request->get('team_uuid'))) {

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
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
        if (!$this->checkTeamOwner($request->get('team_uuid'))) {

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
        }

        $user = $this->userService->findOrFailById($request->get('user_uuid'));
        $model = $this->userTeamService->findOneWhere([
            'user_uuid' => $request->get('user_uuid'),
            'team_uuid' => $request->get('team_uuid')
        ]);
        if (empty($model)) {

            return $this->sendBadRequestJsonResponse(['message' => 'This user is not in the team']);
        }
        $user->userTeamContactLists()->sync($request->get('contact_list_uuids'));

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userResourceClass, $user)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function listMember($id){
        $model = $this->userTeamService->findAllWhere(['team_uuid' => $id]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceCollectionClass, $model)
        );
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function permissionOfTeams($id) {
        if (!$this->checkTeamOwner($id)) {

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $permisions = $this->permissionService->getPermissionOfTeam($team->owner);

        return $this->sendOkJsonResponse(['data' => $permisions]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function contactListOfTeams($id) {
        if (!$this->checkTeamOwner($id)) {

            return $this->sendJsonResponse(false, 'You are not owner of team to set permission', [], 403);
        }
        $team = $this->service->findOrFailById($id);
        $contactLists = $this->contactListService->findAllWhere(['user_uuid' => $team->owner->uuid]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->contactListresourceCollectionClass, $contactLists)
        );
    }

    public function editMy(MyUpdateTeamRequest $request, $id) {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->user()->getKey(),
            'uuid' => $id
        ]);

        $this->service->update($model, $request->all());

        return $this->sendOkJsonResponse(
            $this->service->resourceToData($this->resourceClass, $model)
        );
    }
    public function destroyMy(MyUpdateTeamRequest $request, $id) {
        $model = $this->myService->findOneWhereOrFail([
            'owner_uuid' => auth()->user()->getKey(),
            'uuid' => $id
        ]);

        $this->destroy($model->uuid);

        return $this->sendOkJsonResponse();
    }
}
