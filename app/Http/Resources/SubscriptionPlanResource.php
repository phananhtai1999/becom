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
            'app_uuid' => $this->app_uuid,
            'payment_plan_id' => $this->payment_plan_id,
            'duration_type' => $this->duration_type,
            'duration' => $this->duration,
        ];
        if (\in_array('subscription_plan__app', $expand)) {
            $data['app'] = new AppResource($this->app);
        }

        return $data;
    }
}
