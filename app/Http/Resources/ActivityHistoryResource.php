<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ActivityHistoryResource extends AbstractJsonResource
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
            'type' => $this->type,
            'type_id' => $this->type_id,
            'contact_uuid' => $this->contact_uuid,
            'date' => $this->date,
            'content' => __('activity.'. $this->content['langkey'], $this->content),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('activity_history__contact', $expand)) {
            $data['contact'] = new ContactResource($this->contact);
        }

        return $data;
    }
}
