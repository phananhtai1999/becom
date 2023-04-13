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
            'send_project_uuid' => $this->send_project_uuid,
            'user_uuid' => $this->user_uuid,
            'design' => $this->design,
            'image' => $this->image,
            'type' => $this->type,
            'publish_status' => $this->publish_status,
            'rendered_body' => $this->rendered_body,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('mail_template__send_project', $expand)) {
            $data['send_project'] = new SendProjectResource($this->sendProject);
        }

        if (\in_array('mail_template__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
