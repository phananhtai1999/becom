<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'billing_address_uuid' => $this->billing_address_uuid,
            'product_data' => $this->product_data,
            'payment_method_id' => $this->payment_method_id,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'updated_at' => $this->updated_at
        ];
        if (\in_array('invoice__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }
        if (\in_array('invoice__payment_method', $expand)) {
            $data['payment_method'] = new PaymentMethodResource($this->paymentMethod);
        }
        if (\in_array('invoice__billing_address', $expand)) {
            $data['billing_address'] = new BillingAddressResource($this->billingAddress);
        }

        return $data;
    }
}
