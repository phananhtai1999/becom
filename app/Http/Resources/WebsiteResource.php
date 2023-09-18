<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class WebsiteResource extends AbstractJsonResource
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
            'domain_uuid' => $this->domain_uuid,
            'header_section_uuid' => $this->header_section_uuid,
            'footer_section_uuid' => $this->footer_section_uuid,
            'user_uuid' => $this->user_uuid,
            'publish_status' => $this->publish_status,
            'tracking_ids' => $this->tracking_ids,
            'logo' => $this->logo,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('website__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('website__header_section', $expand)) {
            $data['header_section'] = new SectionTemplateResource($this->headerSection);
        }

        if (\in_array('website__footer_section', $expand)) {
            $data['footer_section'] = new SectionTemplateResource($this->footerSection);
        }

        if (\in_array('website__domain', $expand)) {
            $data['domain'] = new DomainResource($this->domain);
        }

        if (\in_array('website__website_pages', $expand)) {
            $data['website_pages'] = WebsitePageResource::collection($this->websitePages)
            ->map(function ($webPage) {
                $data = $webPage->toArray(null);
                $data['is_homepage'] = $webPage->pivot->is_homepage;
                $data['ordering'] = $webPage->pivot->ordering;
                return $data;
            });
        }

        return $data;
    }
}
