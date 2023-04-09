<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class DomainResource extends AbstractJsonResource
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
            'verified_at' => $this->verified_at,
            'business_uuid' => $this->business_uuid,
            'owner_uuid' => $this->owner_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('domain__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('domain__websites', $expand)) {
            $data['websites'] = WebsiteResource::collection($this->websites);
        }

        return $data;
    }
}
