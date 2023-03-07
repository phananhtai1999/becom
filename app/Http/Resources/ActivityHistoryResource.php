<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ActivityHistoryResource extends AbstractJsonResource
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
            'type' => $this->type,
            'type_id' => $this->type_id,
            'date' => $this->date,
            'content' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->content : $this->getTranslations('content'),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
