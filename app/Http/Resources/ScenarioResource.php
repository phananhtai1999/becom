<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ScenarioResource extends AbstractJsonResource
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
            'name' => $this->name,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('scenario__campaign_scenario', $expand)) {
            $data['campaign_scenario'] = CampaignScenarioResource::collection($this->campaignScenarios);
        }

        return $data;
    }
}
