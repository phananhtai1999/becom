<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CampaignScenarioResource extends AbstractJsonResource
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
            'parent_uuid' => $this->parent_uuid,
            'campaign_uuid' => $this->campaign_uuid,
            'scenario_uuid' => $this->scenario_uuid,
            'type' => $this->type,
            'open_within' => $this->open_within,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('campaign_scenario__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        if (\in_array('campaign_scenario__scenario', $expand)) {
            $data['user'] = new ScenarioResource($this->scenario);
        }

        if (\in_array('campaign_scenario__parent', $expand)) {
            $data['parent'] = new CampaignScenarioResource($this->parentCampaignScenario);
        }

        if (\in_array('campaign_scenario__children', $expand)) {
            $data['children'] = $this->childrenCampaignScenario();
        }

        return $data;
    }
}
