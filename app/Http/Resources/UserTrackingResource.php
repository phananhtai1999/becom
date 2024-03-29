<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class UserTrackingResource extends AbstractJsonResource
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
            'uuid' => $this->getKey(),
            'ip' => $this->ip,
            'user_uuid' => $this->user_uuid,
            'location' => $this->location,
            'postal_code' => $this->postal_code,
            'device' => $this->device,
            'browser' => $this->browser,
            'platform' => $this->platform,
            'is_mobile' => $this->is_mobile,
            'is_desktop' => $this->is_desktop,
            'is_tablet' => $this->is_tablet,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('user_tracking__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
