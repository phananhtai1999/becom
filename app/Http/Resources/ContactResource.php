<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ContactResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'sex' => $this->sex,
            'city' => $this->city,
            'country' => $this->country,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('contact__contact_lists', $expand)) {
            $data['contact_lists'] = ContactListResource::collection($this->contactLists);
        }

        if (\in_array('contact__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        return $data;
    }
}
