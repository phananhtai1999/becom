<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class UserSocialProfileResource extends AbstractJsonResource
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
            'social_network_uuid' => $this->social_network_uuid,
            'social_profile_key' => $this->social_profile_key,
            'other_data' => $this->other_data,
            'social_profile_name' => $this->social_profile_name,
            'social_profile_avatar' => $this->social_profile_avatar,
            'social_profile_email' => $this->social_profile_email,
            'social_profile_phone' => $this->social_profile_phone,
            'social_profile_address' => $this->social_profile_address,
            'updated_info_at' => $this->updated_info_at,
            'user_uuid' => $this->user_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
