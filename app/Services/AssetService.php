<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Asset;
use App\Models\QueryBuilders\AssetQueryBuilder;

class AssetService extends AbstractService
{
    protected $modelClass = Asset::class;

    protected $modelQueryBuilderClass = AssetQueryBuilder::class;
}
