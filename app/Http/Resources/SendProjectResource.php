<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SendProjectResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'domain' => $this->domain,
            'user_uuid' => $this->user_uuid,
            'domain_uuid' => $this->domain_uuid,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'was_verified' => $this->was_verified
        ];

        if (\in_array('send_project__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('send_project__domain', $expand)) {
            $data['domains'] = new DomainResource($this->domains);
        }

        return $data;
    }
}