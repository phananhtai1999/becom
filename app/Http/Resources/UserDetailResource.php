<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class UserDetailResource extends AbstractJsonResource
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
            'about' => $this->about,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
