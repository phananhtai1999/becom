<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CampaignLinkTrackingResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'campaign_uuid' => $this->campaign_uuid,
            'to_url' => $this->to_url,
            'total_click' => $this->total_click,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('campaign_link_tracking__campaign', $expand)) {
            $data['campaign'] = new CampaignResource($this->campaign);
        }

        return $data;
    }
}
