<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'swift_code' => $this->swift_code,
            'bank_name' => $this->bank_name,
            'bank_address' => $this->bank_address,
            'currency' => $this->currency,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
