<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ConfigResource extends AbstractJsonResource
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
            'key' => $this->key,
            'value' => $this->value,
            'group_id' => $this->group_id,
            'default_value' => $this->default_value,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('config__group', $expand)) {
            $data['group'] = new GroupResource($this->group);
        }

        return $data;
    }
}
