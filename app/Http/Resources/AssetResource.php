<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'title' => $this->title,
            'url' => $this->url,
            'asset_group_code' => $this->asset_group_code,
            'asset_size_uuid' => $this->asset_size_uuid,
        ];
    }
}
