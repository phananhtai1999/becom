<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Department;
use App\Models\QueryBuilders\DepartmentQueryBuilder;

class DepartmentService extends AbstractService
{
    protected $modelClass = Department::class;

    protected $modelQueryBuilderClass = DepartmentQueryBuilder::class;

    public function getByProject($id)
    {
        return $this->model->whereHas('sendProjects', function ($query) use ($id) {
            $query->where('send_projects.uuid', $id);
        })->get();
    }
}
