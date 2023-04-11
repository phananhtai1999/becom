<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Company;
use App\Models\PartnerUser;
use App\Models\QueryBuilders\CompanyQueryBuilder;

class PartnerUserService extends AbstractService
{
    protected $modelClass = PartnerUser::class;
}
