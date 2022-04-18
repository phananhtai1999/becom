<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class EmailResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->uuid,
            'email' => $this->email,
            'age' => $this->age,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'state' => $this->state,
            'job' => $this->job,
            'website_uuid' => $this->website_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if(\in_array('email__website', $expand)){
            $data['website'] = new WebsiteResource($this->website);
        }

        return $data;
    }
}
