<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class SectionTemplateResource extends AbstractJsonResource
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
            'section_category_uuid' => $this->section_category_uuid,
            'publish_status' => $this->publish_status,
            'type' => $this->type,
            'html_template' => $this->html_template,
            'css_template' => $this->css_template,
            'display_mode' => $this->display_mode,
            'menu_level' => $this->menu_level,
            'js_template' => $this->js_template,
            'is_default' => $this->is_default,
            'template_json' => $this->template_json,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];


        if (\in_array('section_template__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('section_template__section_category', $expand)) {
            $data['section_category'] = new SectionCategoryResource($this->sectionCategory);
        }

        return $data;
    }
}
