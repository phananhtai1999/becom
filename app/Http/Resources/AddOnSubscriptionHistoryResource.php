<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddOnSubscriptionHistoryResource extends JsonResource
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
            'user_uuid' => $this->user_uuid,
            'add_on_subscription_plan_uuid' => $this->add_on_subscription_plan_uuid,
            'subscription_date' => $this->subscription_date,
            'expiration_date' => $this->expiration_date,
            'status' => $this->status,
            'payment_method_uuid' => $this->payment_method_uuid,
            'invoice_uuid' => $this->invoice_uuid,
            'logs' => $this->logs,
        ];
        if (\in_array('add_on_subscription_history__add_on_subscription_plan', $expand)) {
            $data['add_on_subscription_plan'] = new AddOnSubscriptionPlanResource($this->addOnSubscriptionPlan);
            $data['add_on'] = new AddOnResource(optional($this->addOnSubscriptionPlan)->addOn);
        }
        if (\in_array('add_on_subscription_history__payment_method', $expand)) {
            $data['payment_method'] = new PaymentMethodResource($this->paymentMethod);
        }
        if (\in_array('add_on_subscription_history__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
