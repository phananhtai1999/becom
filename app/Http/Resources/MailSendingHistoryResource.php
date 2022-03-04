<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class MailSendingHistoryResource extends AbstractJsonResource
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
            'uuid' => $this->uuid,
            'campaign_uuid' => $this->campaign_uuid,
            'email' => $this->email,
            'time' => $this->time,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
