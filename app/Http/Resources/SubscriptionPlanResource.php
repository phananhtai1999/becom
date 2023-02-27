<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'platform_package_uuid' => $this->platform_package_uuid,
            'payment_plan_id' => $this->payment_plan_id,
            'duration_type' => $this->duration_type,
            'duration' => $this->duration,
        ];
    }
}
