<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ActivityHistory;
use App\Models\QueryBuilders\MyActivityHistoryQueryBuilder;

class MyActivityHistoryService extends AbstractService
{
    protected $modelClass = ActivityHistory::class;

    protected $modelQueryBuilderClass = MyActivityHistoryQueryBuilder::class;
}
