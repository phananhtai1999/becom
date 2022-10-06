<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\CreditTransactionHistoryQueryBuilder;
use App\Models\CreditTransactionHistory;

class CreditTransactionHistoryService extends AbstractService
{
    protected $modelClass = CreditTransactionHistory::class;

    protected $modelQueryBuilderClass = CreditTransactionHistoryQueryBuilder::class;
}
