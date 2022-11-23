<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class OrderResource extends AbstractJsonResource
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
            'uuid' => $this->getKey(),
            'user_uuid' => $this->user_uuid,
            'payment_method_uuid' => $this->payment_method_uuid,
            'credit' => $this->credit,
            'status' => $this->status,
            'note' => $this->note,
            'total_price' => $this->total_price,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
