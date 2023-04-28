<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerTrackingByYear;
use Carbon\Carbon;

class PartnerTrackingByYearService extends AbstractService
{
    protected $modelClass = PartnerTrackingByYear::class;

    public function earningsOfPartnerByMonth($partnerUuid)
    {
        $monthCurrent = Carbon::today()->month;
        $earningInMonth = $this->model->where([
            ['partner_uuid', $partnerUuid],
            ['year', Carbon::today()->year],
        ])->whereRaw("JSON_EXTRACT(commission, '$.\"$monthCurrent\"') IS NOT NULL")->first();
        return $earningInMonth ? $earningInMonth->commission[Carbon::today()->month] : 0;
    }

    public function trackingEarningsOfPartner($startDate, $endDate, $partnerUuid)
    {
        $earnings = $this->model->where('partner_uuid', $partnerUuid)
            ->whereIn('year', [$startDate->year, $endDate->year])
            ->where(function ($query) use ($startDate, $endDate) {
                for ($month = $startDate->month; $month <= $endDate->month; $month++) {
                    $query->orWhereNotNull("payment->$month");
                }
            })->get();
        $countByMonth = [];
        foreach ($earnings as $earning){
            foreach ($earning->commission as $monthYear => $commission){
                $monthYear = date('Y-m', strtotime($earning->year.'-'.$monthYear));
                if ($monthYear >= $startDate->format('Y-m') && $monthYear <= $endDate->format('Y-m')){
                    $countByMonth[$monthYear] = [
                        'label' => $monthYear,
                        'earnings' => $commission
                    ];
                }
            }
        }
        return array_values($countByMonth);
    }
}
