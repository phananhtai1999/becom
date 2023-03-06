<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Position;
use App\Models\QueryBuilders\PositionQueryBuilder;

class PositionService extends AbstractService
{
    protected $modelClass = Position::class;

    protected $modelQueryBuilderClass = PositionQueryBuilder::class;
}
