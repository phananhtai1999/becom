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

    public function getDepartmentsAssignable($locationUuids, $projectUuid, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $departmentRemoves = $this->getDepartmentsAssignedProject($projectUuid);
        $departmentRemoveUuids = $departmentRemoves->pluck('uuid')->toArray();

        return DepartmentQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereNotIn('uuid', $departmentRemoveUuids)
            ->whereIn('location_uuid', $locationUuids)
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

    public function getIndexMyWithDefault($request, $businessUuid)
    {
        $indexRequest = $this->getIndexRequest($request);
        if (!$businessUuid) {
            return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->where('user_uuid', auth()->userId())
                ->orwhere('manager_uuid', auth()->userId())
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
        } else {
            return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->where('user_uuid', auth()->userId())
                ->orwhere('manager_uuid', auth()->userId())
                ->orWhere(function ($query) use ($businessUuid) {
                    $query->where('business_uuid', $businessUuid)
                        ->where('is_default', true)
                        ->where('status', true);
                })
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
        }

    }

    /**
     * @param $id
     * @return mixed
     */
    public function showMyDepartment($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showMyAndPublicDepartment($id)
    {
        return $this->model->where('uuid', $id)->where(function ($query) {
            $query->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
                ->orWhereNull('user_uuid');
        })->firstOrFail();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyDepartment($id)
    {
        $department = $this->showMyDepartment($id);

        return $this->destroy($department->getKey());
    }
}
