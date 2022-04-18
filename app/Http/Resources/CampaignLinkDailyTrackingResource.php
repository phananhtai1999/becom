<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CampaignLinkDailyTrackingResource extends AbstractJsonResource
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
            'date' => $this->date,
            'to_url' => $this->to_url,
            'total_click' => $this->total_click,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('campaign_link_daily_tracking__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }
        
        return $data;
    }
}
