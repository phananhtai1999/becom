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
            'full_name' => $this->full_name,
            'company_name' => $this->company_name,
            'partner_email' => $this->partner_email,
            'phone_number' => $this->phone_number,
            'user_uuid' => $this->user_uuid,
            'partner_category_uuid' => $this->partner_category_uuid,
            'code' => $this->code,
            'publish_status' => $this->publish_status,
            'answer' => $this->answer,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('partner__partner_category', $expand)) {
            $data['partner_category'] = new PartnerCategoryResource($this->partnerCategory);
        }

        if (\in_array('partner__partner_level', $expand)) {
            $data['partner_level'] = new PartnerLevelResource($this->partnerLevel());
        }

        if (\in_array('partner__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('partner__partner_trackings', $expand)) {
            $data['partner_trackings'] = PartnerTrackingResource::collection($this->partnerTrackings);
        }

        return $data;
    }
}
