<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddOnResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'thumbnail' => $this->thumbnail,
            'payment_product_id' => $this->payment_product_id,
            'status' => $this->status,
            'monthly' => $this->monthly,
            'yearly' => $this->yearly
        ];
        if (\in_array('add_on__permissions', $expand)) {
            $data['permissions'] = PermissionResource::collection($this->permissions);
        }
        if (\in_array('add_on__add_on_subscription_plan', $expand)) {
            $data['add_on_subscription_plan'] = AddOnSubscriptionPlanResource::collection($this->addOnSubscriptionPlans);
        }
        return $data;
    }
}