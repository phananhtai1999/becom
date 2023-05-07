<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerTracking;
use App\Models\QueryBuilders\PartnerTrackingQueryBuilder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;

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
            Log::error($e->getMessage());
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

    public function trackingClickByDateFormat($dateFormat, $startDate, $endDate,$partnerUuid = null)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label, count(uuid) as clicks")
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->when($partnerUuid, function ($query, $partnerUuid) {
                $query->where('partner_uuid', $partnerUuid);
            })
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function getPartnerTrackingChartByGroup($startDate, $endDate, $groupBy, $partnerUuid = null)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $times = [];
        $result = [];
        if ($groupBy == "date"){
            $dateFormat = "%Y-%m-%d";
            $charts = $this->trackingClickByDateFormat($dateFormat, $startDate, $endDate, $partnerUuid)->keyBy('label');
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month"){
            $dateFormat = "%Y-%m";
            $charts = $this->trackingClickByDateFormat($dateFormat, $startDate, $endDate, $partnerUuid)->keyBy('label');

            $period = CarbonPeriod::create($startDate->format('Y-m'), '1 month', $endDate->format('Y-m'));
            foreach ($period as $date) {
                $times[] = $date->format('Y-m');
            }
        }

        foreach ($times as $time) {
            $partnerByTime = $charts->first(function ($item, $key) use ($time){
                return $key === $time;
            });

            if ($partnerByTime){
                $result[] = $partnerByTime->toArray();
            }else{
                $result [] = [
                    'label' => $time,
                    'clicks'  => 0
                ];
            }
        }

        return $result;
    }

    public function getTotalPartnerTrackingChart($startDate, $endDate, $partnerUuid = null)
    {
         return $this->model
             ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                 $query->whereDate('updated_at', '>=', $startDate)
                 ->whereDate('updated_at', '<=', $endDate);
             })
             ->when($partnerUuid, function ($query, $partnerUuid) {
                $query->where('partner_uuid', $partnerUuid);
            })->count();
    }
}
