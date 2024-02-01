<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Techup\ApiList\Http\Resources\GroupApiListResource;

class AppResource extends JsonResource
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
        if (\in_array('app__permissions', $expand)) {
            $data['permissions'] = PermissionResource::collection($this->permissions);
        }
        if (\in_array('app__group_apis', $expand)) {
            $data['group_apis'] = GroupApiListResource::collection($this->groupApis);
        }
        if (\in_array('app__subscription_plan', $expand)) {
            $data['subscription_plan'] = SubscriptionPlanResource::collection($this->subscriptionPlans);
        }
        if (\in_array('app__add_on', $expand)) {
            $data['add_on'] = AddOnResource::collection($this->addOns);
        }

        return $data;
    }
}
