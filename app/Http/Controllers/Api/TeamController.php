<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RestDestroyTrait;
use App\Http\Controllers\Traits\RestEditTrait;
use App\Http\Controllers\Traits\RestIndexTrait;
use App\Http\Controllers\Traits\RestShowTrait;
use App\Http\Controllers\Traits\RestStoreTrait;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\JoinTeamRequest;
use App\Http\Requests\SetContactListRequest;
use App\Http\Requests\SetPermissionForTeamRequest;
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
use App\Services\PermissionService;
use App\Services\SmtpAccountService;
use App\Services\TeamService;
use App\Services\UserContactListService;
use App\Services\UserService;
use App\Services\UserTeamService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    public function __construct(
        TeamService        $service,
        UserTeamService    $userTeamService,
        SmtpAccountService $smtpAccountService,
        UserService        $userService,
        InviteService      $inviteService,
        PermissionService $permissionService,
        ContactListService $contactListService
    )
    {
        $this->service = $service;
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

    public function store(TeamRequest $request)
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
        if ($request->get('type') == Team::LINK_INVITE) {
            $invite = $this->inviteService->create([
                'email' => $request->get('email'),
                'team_uuid' => $request->get('team_uuid'),
                'status' => Invite::NEW_STATUS
            ]);
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
            $this->userTeamService->create(array_merge($request->all(), [
                'user_uuid' => $user->uuid,
            ]));
            $this->smtpAccountService->sendEmailNotificationSystem($user, new SendInviteToTeamByAccount($user, $password));
        }

        return $this->sendCreatedJsonResponse(['url' => env('FRONTEND_URL') . 'api/join-team?team_uuid=' . $request->get('team_uuid')]);
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
        if ($this->service->findOneById($request->get('team_uuid'))->owner_uuid != auth()->user()->getKey()) {

            return $this->sendBadRequestJsonResponse(['message' => 'You are not owner of team to set permission']);
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

    public function setContactList(SetContactListRequest $request)
    {
        if ($this->service->findOneById($request->get('team_uuid'))->owner_uuid != auth()->user()->getKey()) {

            return $this->sendBadRequestJsonResponse(['message' => 'You are not owner of team to set permission']);
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

    public function listMember($id){
        $model = $this->userTeamService->findAllWhere(['team_uuid' => $id]);

        return $this->sendCreatedJsonResponse(
            $this->service->resourceToData($this->userTeamResourceCollectionClass, $model)
        );
    }

    public function permissionOfTeams($id) {
        $team = $this->service->findOrFailById($id);
        $permisions = $this->permissionService->getPermissionOfTeam($team->owner);

        return $this->sendOkJsonResponse(['data' => $permisions]);
    }

    public function contactListOfTeams($id) {
        $team = $this->service->findOrFailById($id);
        $contactLists = $this->contactListService->findAllWhere(['user_uuid' => $team->owner->uuid]);

        return $this->sendOkJsonResponse(
            $this->service->resourceCollectionToData($this->contactListresourceCollectionClass, $contactLists)
        );
    }
}
