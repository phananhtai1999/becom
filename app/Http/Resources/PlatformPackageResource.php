<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformPackageResource extends JsonResource
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
            'monthly' => $this->monthly,
            'yearly' => $this->yearly,
            'description' => $this->description,
            'payment_product_id' => $this->payment_product_id,
            'status' => $this->status,
        ];
        if (\in_array('platform_package__permissions', $expand)) {
            $data['permissions'] = PermissionResource::collection($this->permissions);
        }
        if (\in_array('platform_package__subscription_plan', $expand)) {
            $data['subscription_plan'] = SubscriptionPlanResource::collection($this->subscriptionPlans);
        }

        return $data;
    }
}
