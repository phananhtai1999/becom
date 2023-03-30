<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UnsubscribeResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'code' => $this->getKey(),
            'contact_uuid' => $this->contact_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('unsubscribe__contact', $expand)) {
            $data['contact'] = new ContactResource($this->contact);
        }

        return $data;
    }
}
