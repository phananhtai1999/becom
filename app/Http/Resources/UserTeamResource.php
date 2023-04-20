<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTeamResource extends JsonResource
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
            'team_uuid' => $this->team_uuid,
            'user_uuid' => $this->user_uuid,
            'permission_uuids' => $this->permission_uuids,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (\in_array('user_team__team', $expand)) {
            $data['team'] = new TeamResource($this->team);
        }
        if (\in_array('user_team__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }
        if (\in_array('user_team__contact_lists', $expand)) {
            $data['contact_lists'] = ContactListResource::collection(optional($this->user)->userTeamContactLists);
        }
        if (\in_array('user_team__permissions', $expand)) {
            $data['permissions'] = $this->permissions();
        }

        return $data;
    }
}
