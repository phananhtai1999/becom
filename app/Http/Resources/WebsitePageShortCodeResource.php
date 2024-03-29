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
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'status' => $this->status,
            'key' => $this->key,
            'parent_uuids' => $this->parent_uuids,
            'name' => $this->name,
            'short_code' => $this->short_code,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
            'deleted_at' => $this->deleted_at,
        ];

        if (\in_array('website_page_short_code__children_short_code', $expand)) {
            $data['children_short_code'] = self::collection($this->childrenWebsitePageShortCode());
        }

        if (\in_array('website_page_short_code__parent_short_code', $expand)) {
            $data['parent_short_code'] = self::collection($this->parentWebsitePageShortCode());
        }

        return $data;
    }
}
