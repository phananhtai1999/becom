<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ParagraphType;
use App\Models\QueryBuilders\ParagraphTypeQueryBuilder;

class ParagraphTypeService extends AbstractService
{
    protected $modelClass = ParagraphType::class;

    protected $modelQueryBuilderClass = ParagraphTypeQueryBuilder::class;
}
