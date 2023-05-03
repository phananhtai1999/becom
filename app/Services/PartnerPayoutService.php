<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerPayout;
use App\Models\QueryBuilders\PartnerPayoutQueryBuilder;

class PartnerPayoutService extends AbstractService
{
    protected $modelClass = PartnerPayout::class;

    protected $modelQueryBuilderClass = PartnerPayoutQueryBuilder::class;

    public function getTotalAmountUsedOfPartner($partnerUuid)
    {
        return $this->model->where('partner_uuid', $partnerUuid)
            ->whereIn('status', ['new', 'accept'])->selectRaw('Sum(amount) as total_amounts')->first()->total_amounts;
    }

}
