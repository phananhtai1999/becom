<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddOnSubscriptionPlanResource extends JsonResource
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
            'add_on_uuid' => $this->add_on_uuid,
            'payment_plan_id' => $this->payment_plan_id,
            'duration_type' => $this->duration_type,
            'duration' => $this->duration,
        ];
        if (\in_array('subscription_plan__add_on', $expand)) {
            $data['add_on'] = new AddOnResource($this->addOn);
        }

        return $data;
    }
}
