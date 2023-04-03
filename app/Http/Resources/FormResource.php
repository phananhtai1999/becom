<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FormResource extends AbstractJsonResource
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
            'uuid' => $this->uuid,
            'title' => $this->title,
            'user_uuid' => $this->user_uuid,
            'contact_list_uuid' => $this->contact_list_uuid,
            'display_type' => $this->display_type,
            'publish_status' => $this->publish_status,
            'template' => $this->template,
            'template_json' => $this->template_json,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (\in_array('form__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('form__contact_list', $expand)) {
            $data['contact_list'] = new ContactListResource($this->contactList);
        }

        return $data;
    }
}
