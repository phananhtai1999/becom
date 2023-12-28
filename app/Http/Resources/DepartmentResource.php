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
            'business_uuid' => $this->business_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('department__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('department__user_role', $expand)) {
            $data['user_role'] = RoleResource::collection(optional(optional($this->user)->roles));
        }

        if (\in_array('department__teams', $expand)) {
            $data['teams'] = TeamResource::collection($this->teams);
        }

        return $data;
    }
}
