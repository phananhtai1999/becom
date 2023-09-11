<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class WebsitePageResource extends AbstractJsonResource
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
            'uuid' => $this->getKey(),
            'title' => $this->title,
            'user_uuid' => $this->user_uuid,
            'website_page_category_uuid' => $this->website_page_category_uuid,
            'display_type' => $this->display_type,
            'template' => $this->template,
            'template_json' => $this->template_json,
            'publish_status' => $this->publish_status,
            'reject_reason' => $this->reject_reason,
            'keyword' => $this->keyword,
            'keywords' => $this->keywords,
            'description' => $this->description,
            'descriptions' => $this->descriptions,
            'feature_image' => $this->feature_image,
            'is_default' => $this->is_default,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];


        if (\in_array('website_page__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('website_page__website_page_category', $expand)) {
            $data['website_page_category'] = new WebsitePageCategoryResource($this->websitePageCategory);
        }

        if (\in_array('website_page__website', $expand)) {
            $data['website'] = new WebsiteResource($this->websites());
        }

        return $data;
    }
}
