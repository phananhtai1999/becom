<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Department;
use App\Models\QueryBuilders\DepartmentQueryBuilder;

class DepartmentService extends AbstractService
{
    protected $modelClass = Department::class;

    protected $modelQueryBuilderClass = DepartmentQueryBuilder::class;
}
