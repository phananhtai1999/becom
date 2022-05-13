<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class SendEmailScheduleLogResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'campaign_uuid' => $this->campaign_uuid,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_running' => $this->is_running,
            'was_crashed' => $this->was_crashed,
            'log' => $this->log,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
