<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CompanyResource extends AbstractJsonResource
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
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('company__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('company__user_role', $expand)) {
            $data['user_role'] = RoleResource::collection(optional(optional($this->user)->roles));
        }

        return $data;
    }
}
