<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerTracking;
use App\Models\QueryBuilders\PartnerTrackingQueryBuilder;
use Carbon\Carbon;

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

    public function trackingClicksOfPartnerInMonth($partnerUuid){
        return $this->model->where('partner_uuid', $partnerUuid)
            ->whereMonth('created_at', Carbon::today()->month)
            ->whereYear('created_at', Carbon::today()->year)->get();
    }

    public function getTop10PartnerClick()
    {
        return $this->model->join('partners as p', 'p.uuid', '=', 'partner_trackings.partner_uuid')
            ->selectRaw("count(partner_trackings.uuid) as count, concat(p.first_name, ' ', p.last_name) as full_name, p.partner_email")
            ->orderBy('count', 'DESC')->groupByRaw("full_name, p.partner_email")
            ->skip(0)->take(10)->get();
    }

    public function trackingClickByDateFormat($dateFormat, $startDate, $endDate,$partnerUuid)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label, count(uuid) as clicks")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('partner_uuid', $partnerUuid)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }
}
