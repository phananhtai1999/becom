<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CreditPackageResource extends AbstractJsonResource
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
            'price' => $this->price,
            'credit' => $this->credit
        ];
    }
}
