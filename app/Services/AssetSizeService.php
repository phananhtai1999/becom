<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AssetSize;
use App\Models\QueryBuilders\AssetSizeQueryBuilder;

class AssetSizeService extends AbstractService
{
    protected $modelClass = AssetSize::class;

    protected $modelQueryBuilderClass = AssetSizeQueryBuilder::class;
}
