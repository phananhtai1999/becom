<?php

namespace App\Http\Resources;

use App\Models\App;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAppResource extends JsonResource
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
            'app_uuid' => $this->app_uuid,
            'subscription_plan_uuid' => $this->subscription_plan_uuid,
            'expiration_date' => $this->expiration_date,
            'auto_renew' => $this->auto_renew,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if (\in_array('user_app__app', $expand)) {
            $data['platform_package'] = new AppResource($this->platformPackage);
        }
        if (\in_array('user_app__subscription_plan', $expand)) {
            $data['subscription_plan'] = new SubscriptionPlanResource($this->subscriptionPlan);
        }

        return $data;
    }
}
