<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CreditHistoryResource extends AbstractJsonResource
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
            'campaign_uuid' => $this->campaign_uuid,
            'credit' => $this->credit,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
