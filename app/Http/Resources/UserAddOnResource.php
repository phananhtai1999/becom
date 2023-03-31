<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddOnResource extends JsonResource
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
            'add_on_subscription_plan_uuid' => $this->add_on_subscription_plan_uuid,
            'expiration_date' => $this->expiration_date,
            'auto_renew' => $this->auto_renew,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (\in_array('user_add_on__add_on_subscription_plan', $expand)) {
            $data['add_on_subscription_plan'] = new AddOnSubscriptionPlanResource($this->addOnSubscriptionPlan);
            $data['add_on'] = new AddOnResource(optional($this->addOnSubscriptionPlan)->addOn);
        }
        if (\in_array('user_add_on__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
