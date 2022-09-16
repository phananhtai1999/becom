<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;

class CreditHistoryService extends AbstractService
{
    protected $modelClass = UserCreditHistory::class;

    protected $modelQueryBuilderClass = UserCreditHistoryQueryBuilder::class;
}
