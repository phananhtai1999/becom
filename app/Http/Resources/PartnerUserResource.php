<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class PartnerUserResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->getKey(),
            'user_uuid' => $this->user_uuid,
            'partner_code' => $this->partner_code,
            'registered_from_partner_code' => $this->registered_from_partner_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
