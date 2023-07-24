<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class PartnerCategoryResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'image' => $this->image,
            'title' => $this->title,
            'titles' => $this->titles,
            'content' => $this->content,
            'contents' => $this->contents,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('partner_category__partners', $expand)) {
            $data['partners'] = PartnerResource::collection($this->partners);
        }

        return $data;
    }
}
