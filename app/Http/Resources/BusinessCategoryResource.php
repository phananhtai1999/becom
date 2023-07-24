<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class BusinessCategoryResource extends AbstractJsonResource
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
            'title' => $this->title,
            'titles' => $this->titles,
            'parent_uuid' => $this->parent_uuid,
            'publish_status' => $this->publish_status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('business_category__parent_category', $expand)) {
            $data['parent_category'] = new BusinessCategoryResource($this->parentBusinessCategory);
        }

        if (\in_array('business_category__children_category', $expand)) {
            $data['children_category'] = self::collection($this->childrenBusinessCategory);
        }

        if (\in_array('business_category__children_category_public', $expand)) {
            $data['children_category_public'] = self::collection($this->childrenBusinessCategoryPublic);
        }

        if (\in_array('business_category__contacts', $expand)) {
            $data['contacts'] = ContactResource::collection($this->contacts);
        }

        return $data;
    }
}
