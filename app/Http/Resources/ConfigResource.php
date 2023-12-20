<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Techup\ApiConfig\Services\ConfigService;

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
        //Multi lang for value with meta_tag type
        $value = (new ConfigService())->multiLangForValueWithMetaTagType($this->type, $this->value);

        $data = [
            'uuid' => $this->getKey(),
            'key' => $this->key,
            'value' => $value,
            'type' => $this->type,
            'status' => $this->status,
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
