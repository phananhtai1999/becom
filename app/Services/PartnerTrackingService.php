<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerTracking;
use App\Models\QueryBuilders\PartnerTrackingQueryBuilder;

class PartnerTrackingService extends AbstractService
{
    protected $modelClass = PartnerTracking::class;

    protected $modelQueryBuilderClass = PartnerTrackingQueryBuilder::class;

    public function storePartnerTracking($partner)
    {
        $country = null;
        try {
            $ip = geoip()->getClientIP();
            $country = geoip()->getLocation($ip)->country;
        } catch (\Exception $e) {
            $ip = request()->ip();
        }

        //Kiểm tra ip đã từng click vào partner đó hay chưa. chưa thì create.
        $partnerTracking = $this->findOneWhere([
            ['ip', $ip],
            ['partner_uuid', $partner->uuid]
        ]);
        if (!$partnerTracking) {
            $this->create([
                'partner_uuid' => $partner->uuid,
                'ip' => $ip,
                'country' => $country
            ]);
        }
    }
}
