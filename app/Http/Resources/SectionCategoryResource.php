<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class SectionCategoryResource extends AbstractJsonResource
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
            'title' => app(UserService::class)->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('section_category__section_templates', $expand)) {
            $data['section_templates'] = SectionTemplateResource::collection($this->websitePages);
        }

        return $data;
    }
}
