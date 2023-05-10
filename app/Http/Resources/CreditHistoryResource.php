<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CreditHistoryResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);
        $data = [
            'uuid' => $this->getKey(),
            'user_uuid' => $this->user_uuid,
            'campaign_uuid' => $this->campaign_uuid,
            'scenario_uuid' => $this->scenario_uuid,
            'credit' => $this->credit,
            'type' => $this->type,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('credit_history__user', $expand)) {

            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('credit_history__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        if (\in_array('credit_history__scenario', $expand)) {
            $data['scenario'] = new ScenarioResource($this->scenario);
        }

        return $data;
    }
}
