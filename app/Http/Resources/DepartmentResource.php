<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class DepartmentResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'name' => $this->name,
            'names' => $this->names,
            'user_uuid' => $this->user_uuid,
            'is_default' => $this->is_default,
            'manager_uuid' => $this->manager_uuid,
            'business_uuid' => $this->business_uuid,
            'location_uuid' => $this->location_uuid,
            'department_code' => $this->department_code,
            'status' => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('department__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('department__manager', $expand)) {
            $data['manager'] = new UserResource($this->manager);
        }

        if (\in_array('department__user_role', $expand)) {
            $data['user_role'] = RoleResource::collection(optional(optional($this->user)->roles));
        }

        if (\in_array('department__teams', $expand)) {
            $data['teams'] = TeamResource::collection($this->teams);
        }

        if (\in_array('department__location', $expand)) {
            $data['location'] = new LocationResource($this->location);
        }

        if (\in_array('department__send_projects', $expand)) {
            $data['send_projects'] = SendProjectResource::collection($this->sendProjects);
        }

        return $data;
    }
}
