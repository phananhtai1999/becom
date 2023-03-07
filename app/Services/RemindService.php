<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\RemindQueryBuilder;
use App\Models\Remind;

class RemindService extends AbstractService
{
    protected $modelClass = Remind::class;

    protected $modelQueryBuilderClass = RemindQueryBuilder::class;
}
