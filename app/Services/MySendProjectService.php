<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MySendProjectQueryBuilder;
use App\Models\SendProject;

class MySendProjectService extends AbstractService
{
    protected $modelClass = SendProject::class;

    protected $modelQueryBuilderClass = MySendProjectQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */

}
