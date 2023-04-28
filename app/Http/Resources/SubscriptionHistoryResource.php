<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionHistoryResource extends JsonResource
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
            'subscription_plan_uuid' => $this->subscription_plan_uuid,
            'subscription_date' => $this->subscription_date,
            'expiration_date' => $this->expiration_date,
            'status' => $this->status,
            'payment_method_uuid' => $this->payment_method_uuid,
            'invoice_uuid' => $this->invoice_uuid,
            'logs' => $this->logs,
        ];
        if (\in_array('subscription_history__subscription_plan', $expand)) {
            $data['subscription_plan'] = new SubscriptionPlanResource($this->subscriptionPlan);
            $data['platform_package'] = new PlatformPackageResource(optional($this->subscriptionPlan)->platformPackage);
        }
        if (\in_array('subscription_history__payment_method', $expand)) {
            $data['payment_method'] = new PaymentMethodResource($this->paymentMethod);
        }
        if (\in_array('subscription_history__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }
        if (\in_array('subscription_history__invoice', $expand)) {
            $data['invoice'] = new InvoiceResource($this->invoice);
        }
        return $data;
    }
}
