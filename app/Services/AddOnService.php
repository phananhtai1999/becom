<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOn;
use App\Models\QueryBuilders\AddOnQueryBuilder;

class AddOnService extends AbstractService
{
    protected $modelClass = AddOn::class;

    protected $modelQueryBuilderClass = AddOnQueryBuilder::class;
}
