<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class MailTemplateResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'subject' => $this->subject,
            'body' => $this->body,
            'website_uuid' => $this->website_uuid,
            'rendered_body' => $this->rendered_body,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('mail_template__website', $expand)) {
            $data['website'] = new WebsiteResource($this->website);
        }

        return $data;
    }
}
