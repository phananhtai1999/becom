<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'national_flag' => $this->national_flag,
            'country_code' => $this->country_code,
            'name' => $this->name,
            'country_phone_code' => $this->country_phone_code,
            'sms_price' => $this->sms_price,
            'email_price' => $this->email_price,
            'telegram_price' => $this->telegram_price,
            'viber_price' => $this->viber_price,
        ];
    }
}
