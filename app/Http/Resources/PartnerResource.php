<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class PartnerResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'work_email' => $this->work_email,
            'phone_number' => $this->phone_number,
            'partner_category_uuid' => $this->partner_category_uuid,
            'publish_status' => $this->publish_status,
            'answer' => $this->answer,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('partner__partner_category', $expand)) {
            $data['partner_category'] = new PartnerCategoryResource($this->partnerCategory);
        }

        return $data;
    }
}
