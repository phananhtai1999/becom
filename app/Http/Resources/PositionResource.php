<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class PositionResource extends AbstractJsonResource
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
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('position__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}