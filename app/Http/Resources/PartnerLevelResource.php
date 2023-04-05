<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use App\Services\UserService;

class PartnerLevelResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $data = [
            'uuid' => $this->getKey(),
            'title' => (new UserService())->checkLanguagesPermission() ? $this->getTranslations('title') : $this->title,
            'title_translate' => $this->title_translate,
            'number_of_references' => $this->number_of_references,
            'commission' => $this->commission,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $data;
    }
}
