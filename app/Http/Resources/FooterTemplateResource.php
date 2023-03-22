<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class FooterTemplateResource extends AbstractJsonResource
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
            'active_by_uuid' => $this->active_by_uuid,
            'is_default' => $this->is_default,
            'type' => $this->type,
            'publish_status' => $this->publish_status,
            'template_type' => $this->template_type,
            'template' => $this->template,
            'template_json' => $this->template_json,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];


        if (\in_array('footer_template__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('footer_template__active_by', $expand)) {
            $data['active_by'] = new UserResource($this->activeBy);
        }

        return $data;
    }
}
