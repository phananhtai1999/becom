<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class LanguageResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'code' => $this->getKey(),
            'name' => $this->name,
            'flag_image' => $this->flag_image,
            'status' => $this->status,
            'fe' => $this->fe,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $data;
    }
}
