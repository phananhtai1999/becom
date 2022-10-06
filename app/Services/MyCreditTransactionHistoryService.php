<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CreditTransactionHistory;
use App\Models\QueryBuilders\MyCreditTransactionHistoryQueryBuilder;

class MyCreditTransactionHistoryService extends AbstractService
{
    protected $modelClass = CreditTransactionHistory::class;

    protected $modelQueryBuilderClass = MyCreditTransactionHistoryQueryBuilder::class;
}
