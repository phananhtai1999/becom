<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChildrenTeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'leader_uuid' => $this->leader_uuid,
            'num_of_team_member' => $this->NumOfTeamMember,
            'owner_uuid' => $this->owner_uuid,
            'parent_team_uuid' => $this->parent_team_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
