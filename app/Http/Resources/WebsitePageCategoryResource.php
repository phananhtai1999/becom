<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class WebsitePageCategoryResource extends AbstractJsonResource
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
            'title' => auth()->user()->roles->where('slug', 'admin')->isEmpty() ? $this->title : $this->getTranslations('title'),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('website_page_category__website_pages', $expand)) {
            $data['website_pages'] = WebsitePageResource::collection($this->websitePages);
        }

        return $data;
    }
}
