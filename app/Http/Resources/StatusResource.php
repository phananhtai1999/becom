<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class StatusResource extends AbstractJsonResource
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
            'name' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->name : $this->getTranslations('name'),
            'name_translate' => $this->name_translate,
            'points' => $this->points,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('status__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('status__user_role', $expand)) {
            $data['user_role'] = RoleResource::collection(optional(optional($this->user)->roles));
        }

        return $data;
    }
}
