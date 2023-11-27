<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);
        $data = [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'user_uuid' => $this->user_uuid,
            'address' => $this->address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
        if (\in_array('location__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('location__teams', $expand)) {
            $data['teams'] = TeamResource::collection($this->teams);
        }

        return $data;
    }
}
