<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class CreditTransactionHistoryResource extends AbstractJsonResource
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
            'credit' => $this->credit,
            'campaign_uuid' => $this->campaign_uuid,
            'scenario_uuid' => $this->scenario_uuid,
            'add_by_uuid' => $this->add_by_uuid,
            'created_at' => $this->created_at
        ];

        if (\in_array('credit_transaction_history__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('credit_transaction_history__add_by', $expand)) {
            $data['add_by'] = new UserResource($this->add_by);
        }

        if (\in_array('credit_transaction_history__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        if (\in_array('credit_transaction_history__scenario', $expand)) {
            $data['scenario'] = new ScenarioResource($this->scenario);
        }

        return $data;
    }
}
