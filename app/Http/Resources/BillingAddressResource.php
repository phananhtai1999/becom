<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillingAddressResource extends JsonResource
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

        $data =  [
            'name' => $this->name,
            'user_uuid' => $this->user_uuid,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'company' => $this->company,
            'country' => $this->country,
            'city' => $this->city,
            'state' => $this->state,
            'zipcode' => $this->zipcode,
            'type' => $this->type,
            'is_default' => $this->is_default,
        ];
        if (\in_array('billing_address__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
