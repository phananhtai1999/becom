<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class MailOpenTrackingResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'uuid' => $this->uuid,
            'mail_sending_history_uuid' => $this->mail_sending_history_uuid,
            'ip' => $this->ip,
            'user_agent' => $this->user_agent,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $data;
    }
}
