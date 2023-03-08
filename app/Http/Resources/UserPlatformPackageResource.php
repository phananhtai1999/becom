<?php

namespace App\Http\Resources;

use App\Models\PlatformPackage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPlatformPackageResource extends JsonResource
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
            'user_uuid' => $this->user_uuid ,
            'platform_package_uuid' => $this->platform_package_uuid,
            'subscription_plan_uuid' => $this->subscription_plan_uuid,
            'expiration_date' => $this->expiration_date,
            'auto_renew' => $this->auto_renew,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (\in_array('user_platform_package__platform_package', $expand)) {
            $data['platform_package'] = new PlatformPackageResource($this->platformPackage);
        }
        if (\in_array('user_platform_package__subscription_plan', $expand)) {
            $data['subscription_plan'] = new SubscriptionPlanResource($this->subscriptionPlan);
        }

        return $data;
    }
}
