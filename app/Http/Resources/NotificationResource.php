<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class NotificationResource extends AbstractJsonResource
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
            'type' => $this->type,
            'type_uuid' => $this->type_uuid,
            'user_uuid' => $this->user_uuid,
            'content' => __('notification.'. $this->content['langkey'], $this->content),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('notification__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('notification__campaign', $expand) && $this->type === 'campaign') {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        if (\in_array('notification__scenario', $expand) && $this->type === 'scenario') {
            $data['scenario'] = new ScenarioResource($this->scenario);
        }
        return $data;
    }
}
