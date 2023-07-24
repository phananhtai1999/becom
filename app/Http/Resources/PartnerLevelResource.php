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
            'image' => $this->image,
            'title' => $this->title,
            'titles' => $this->titles,
            'number_of_customers' => $this->number_of_customers,
            'commission' => $this->commission,
            'content' => $this->content,
            'contents' => $this->contents,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        return $data;
    }
}
