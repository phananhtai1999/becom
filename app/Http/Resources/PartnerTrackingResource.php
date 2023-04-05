<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class PartnerTrackingResource extends AbstractJsonResource
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
            'uuid' => $this->getKey(),
            'partner_uuid' => $this->partner_uuid,
            'ip' => $this->ip,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('partner_tracking__partner', $expand)) {
            $data['partner'] = new PartnerResource($this->partner);
        }

        return $data;
    }
}
