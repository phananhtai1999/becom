<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class UserConfigResource extends AbstractJsonResource
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
            'app_language' => $this->app_language,
            'user_language' => $this->user_language,
            'display_name_style' => $this->display_name_style,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
