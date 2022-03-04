<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CampaignResource extends AbstractJsonResource
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
            'tracking_key' => $this->tracking_key,
            'mail_template_uuid' => $this->mail_template_uuid,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'number_email_per_date' => $this->number_email_per_date,
            'number_email_per_user' => $this->number_email_per_user,
            'status' => $this->status,
            'smtp_account_uuid' => $this->smtp_account_uuid,
            'website_uuid' => $this->website_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
