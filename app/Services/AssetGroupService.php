<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AssetGroup;
use App\Models\QueryBuilders\AssetGroupQueryBuilder;

class AssetGroupService extends AbstractService
{
    protected $modelClass = AssetGroup::class;

    protected $modelQueryBuilderClass = AssetGroupQueryBuilder::class;
}
