<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBusinessResource extends JsonResource
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
            'uuid' => $this->getKey(),
            'user_uuid' => $this->user_uuid,
            'business_uuid' => $this->business_uuid,
            'is_blocked' => $this->is_blocked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];

        if (\in_array('user_business__business', $expand)) {
            $data['business'] = new BusinessCategoryResource($this->business);
        }
        if (\in_array('user_business__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }
        if (\in_array('user_business__team', $expand)) {
            $data['teams'] = TeamResource::collection(optional(optional($this->user)->teams));
        }

        return $data;
    }
}
