<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\CreditHistoryQueryBuilder;
use App\Models\CreditHistory;

class CreditHistoryService extends AbstractService
{
    protected $modelClass = CreditHistory::class;

    protected $modelQueryBuilderClass = CreditHistoryQueryBuilder::class;
}
