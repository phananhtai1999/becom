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
use App\Http\Requests\SetPermissionForTeamRequest;
use App\Http\Requests\TeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TeamResourceCollection;
use App\Http\Resources\UserTeamResource;
use App\Mail\SendInviteToTeam;
use App\Services\SmtpAccountService;
use App\Services\TeamService;
use App\Services\UserService;
use App\Services\UserTeamService;

class TeamController extends Controller
{
    use RestIndexTrait, RestShowTrait, RestDestroyTrait, RestEditTrait;

    public function __construct(
        TeamService        $service,
        UserTeamService    $userTeamService,
        SmtpAccountService $smtpAccountService,
        UserService        $userService
    )
    {
        $this->service = $service;
        $this->smtpAccountService = $smtpAccountService;
        $this->userTeamService = $userTeamService;
        $this->userService = $userService;
        $this->resourceCollectionClass = TeamResourceCollection::class;
        $this->userTeamResourceClass = UserTeamResource::class;
        $this->resourceClass = TeamResource::class;
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
        $user = $this->userService->findOrFailById($request->get('user_uuid'));
        $url = env('FRONTEND_URL') . 'api/join-team?team_uuid=' . $request->get('team_uuid');
        $this->smtpAccountService->sendEmailNotificationSystem($user, new SendInviteToTeam($user, $url));

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
}
