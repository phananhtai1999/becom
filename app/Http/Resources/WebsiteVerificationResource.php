<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WebsiteVerificationResource extends AbstractJsonResource
{

    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'website_uuid' => $this->website_uuid,
            'token' => $this->token,
            'verified_at' => $this->verified_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'was_verified' => $this->was_verified
        ];
    }
}
