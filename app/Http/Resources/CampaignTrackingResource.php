<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CampaignTrackingResource extends AbstractJsonResource
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
            'uuid' => $this->getKey(),
            'campaign_uuid' => $this->campaign_uuid,
            'total_open' => $this->total_open,
            'total_link_click' => $this->total_link_click,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('campaign_tracking__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        return $data;
    }
}
