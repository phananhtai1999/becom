<?php

namespace App\Http\Resources;

use App\Http\Requests\LocationRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);
        $data = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'leader_uuid' => $this->leader_uuid,
            'department_uuid' => $this->department_uuid,
            'location_uuid' => $this->location_uuid,
            'num_of_team_member' => $this->NumOfTeamMember,
            'owner_uuid' => $this->owner_uuid,
            'parent_team_uuid' => $this->parent_team_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (\in_array('team__owner', $expand)) {
            $data['owner'] = new UserResource($this->owner);
        }

        if (\in_array('team__children_team', $expand)) {
            $data['children_team'] = ChildrenTeamResource::collection($this->childrenTeam);
        }

        if (\in_array('team__parent_team', $expand)) {
            $data['parent_team'] = new TeamResource($this->parentTeam);
        }

        if (\in_array('team__department', $expand)) {
            $data['department'] = new DepartmentResource($this->department);
        }

        if (\in_array('team__location', $expand)) {
            $data['location'] = new LocationResource($this->location);
        }

        if (\in_array('team__team_members', $expand)) {
            $data['team_member'] = UserTeamResource::collection($this->userTeam);
        }

        if (\in_array('team__send_projects', $expand)) {
            $data['send_projects'] = SendProjectResource::collection($this->sendProjects);
        }

        return $data;
    }
}
