<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class PartnerPayoutResource extends AbstractJsonResource
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
            'partner_uuid' => $this->partner_uuid,
            'amount' => $this->amount,
            'status' => $this->status,
            'time' => $this->time,
            'by_user_uuid' => $this->by_user_uuid,
            'payout_method_uuid' => $this->payout_method_uuid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('partner_payout__by_user', $expand)) {
            $data['by_user'] = new UserResource($this->byUser);
        }

        if (\in_array('partner_payout__partner', $expand)) {
            $data['partner'] = new PartnerResource($this->partner);
        }
        if (\in_array('partner_payout__payout_method', $expand)) {
            $data['payout_method'] = new PayoutMethodResource($this->payoutMethod);
        }
        return $data;
    }
}
