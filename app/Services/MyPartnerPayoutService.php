<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerPayout;
use App\Models\QueryBuilders\MyPartnerPayoutQueryBuilder;

class MyPartnerPayoutService extends AbstractService
{
    protected $modelClass = PartnerPayout::class;

    protected $modelQueryBuilderClass = MyPartnerPayoutQueryBuilder::class;

}
