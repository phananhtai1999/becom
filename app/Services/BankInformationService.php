<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\BankInformation;
use App\Models\QueryBuilders\BankInformationQueryBuilder;

class BankInformationService extends AbstractService
{
    protected $modelClass = BankInformation::class;

    protected $modelQueryBuilderClass = BankInformationQueryBuilder::class;
}
