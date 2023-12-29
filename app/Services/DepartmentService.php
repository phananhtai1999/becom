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

    public function getLocationsAssignable($businessUuid, $projectUuid, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $departmentRemoves = $this->getDepartmentsAssignedProject($projectUuid);
        $departmentRemoveUuids = $departmentRemoves->pluck('uuid')->toArray();

        return DepartmentQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereNotIn('uuid', $departmentRemoveUuids)
            ->where('business_uuid', $businessUuid)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getDepartmentsAssignedProject($projectUuid)
    {

        return $this->model->whereHas('sendProjects', function ($q) use ($projectUuid) {
            $q->where('send_projects.uuid', $projectUuid);
        })->get();
    }

    public function getByTeam($id)
    {
        return $this->model->whereHas('teams', function ($query) use ($id) {
            $query->where('teams.uuid', $id);
        })->get();
    }
}
