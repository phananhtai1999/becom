<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ActivityHistory;
use App\Models\QueryBuilders\ActivityHistoryQueryBuilder;

class ActivityHistoryService extends AbstractService
{
    protected $modelClass = ActivityHistory::class;

    protected $modelQueryBuilderClass = ActivityHistoryQueryBuilder::class;
}
