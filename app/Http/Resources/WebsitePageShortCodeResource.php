<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsitePageShortCodeResource extends JsonResource
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
            'type' => $this->type,
            'key' => $this->key,
            'parent_uuid' => $this->parent_uuid,
            'name' => $this->name,
            'short_code' => $this->short_code,
        ];
    }
}
