<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class RemindResource extends AbstractJsonResource
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
            'name' => $this->name,
            'note' => $this->note,
            'date' => $this->date,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('remind__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('remind__contacts', $expand)) {
            $data['contact'] = ContactResource::collection($this->contacts);
        }

        if (\in_array('remind__activity_histories', $expand)) {
            $data['activity_histories'] = ActivityHistoryResource::collection($this->activityHistories);
        }

        return $data;
    }
}
