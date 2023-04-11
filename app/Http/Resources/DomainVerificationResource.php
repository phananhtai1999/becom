<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class DomainVerificationResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'domain_uuid' => $this->domain_uuid,
            'token' => $this->token,
            'verified_at' => $this->verified_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'was_verified' => $this->was_verified
        ];
    }
}
