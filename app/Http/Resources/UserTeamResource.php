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
        if (\in_array('team__owner', $expand)) {
            $data['owner'] = new UserResource($this->owner);
        }

        return $data;
    }
}
