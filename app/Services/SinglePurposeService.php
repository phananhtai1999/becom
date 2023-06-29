<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SinglePurposeQueryBuilder;
use App\Models\SinglePurpose;

class SinglePurposeService extends AbstractService
{
    protected $modelClass = SinglePurpose::class;

    protected $modelQueryBuilderClass = SinglePurposeQueryBuilder::class;
}
