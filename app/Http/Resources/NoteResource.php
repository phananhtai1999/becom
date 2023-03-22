<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class NoteResource extends AbstractJsonResource
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
            'note' => $this->note,
            'user_uuid' => $this->user_uuid,
            'contact_uuid' => $this->contact_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('note__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('note__contact', $expand)) {
            $data['contact'] = new ContactResource($this->contact);
        }

        if (\in_array('note__activity_histories', $expand)) {
            $data['activity_histories'] = ActivityHistoryResource::collection($this->activityHistories);
        }

        return $data;
    }
}
