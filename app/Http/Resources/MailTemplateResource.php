<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MailTemplateResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
