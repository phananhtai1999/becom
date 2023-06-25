<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayoutMethodResource extends JsonResource
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
            'type' => $this->type,
            'email' => $this->email,
            'account_number' => $this->account_number,
            'payout_fee' => $this->payout_fee,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'name_on_account' => $this->name_on_account,
            'user_uuid' => $this->user_uuid,
            'is_default' => $this->is_default,
            'swift_code' => $this->swift_code,
            'bank_name' => $this->bank_name,
            'bank_address' => $this->bank_address,
            'currency' => $this->currency,
            'last_4' => $this->last_4
        ];
        if (\in_array('payout_method__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
