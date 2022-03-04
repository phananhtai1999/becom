<?php

namespace App\Abstracts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AbstractJsonResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
