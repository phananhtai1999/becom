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
        return $this->model->where([
            ['partner_uuid', $partnerUuid],
            ['year', Carbon::today()->year],
        ])->whereRaw("JSON_EXTRACT(commission, '$.".Carbon::today()->month."') IS NOT NULL")->first()->commission[Carbon::today()->month];
    }
}
