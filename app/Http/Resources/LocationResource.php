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
            'business_uuid' => $this->business_uuid,
            'manager_uuid' => $this->manager_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
        if (\in_array('location__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('location__manager', $expand)) {
            $data['manager_uuid'] = new UserResource($this->manager);
        }

        if (\in_array('location__teams', $expand)) {
            $data['teams'] = TeamResource::collection($this->teams);
        }

        if (\in_array('location__send_projects', $expand)) {
            $data['send_projects'] = SendProjectResource::collection($this->sendProjects);
        }

        return $data;
    }
}
