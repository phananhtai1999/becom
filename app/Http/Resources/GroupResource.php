<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class GroupResource extends AbstractJsonResource
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
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('group__configs', $expand)) {
            $data['config'] = ConfigResource::collection(($this->configs));
        }

        return $data;
    }
}
