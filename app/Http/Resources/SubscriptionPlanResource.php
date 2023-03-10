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
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'platform_package_uuid' => $this->platform_package_uuid,
            'payment_plan_id' => $this->payment_plan_id,
            'duration_type' => $this->duration_type,
            'duration' => $this->duration,
        ];
        if (\in_array('subscription_plan__platform_package', $expand)) {
            $data['platform_package'] = new PlatformPackageResource($this->platformPackage);
        }

        return $data;
    }
}
