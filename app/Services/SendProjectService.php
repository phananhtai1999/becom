<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SendProjectQueryBuilder;
use App\Models\QueryBuilders\TeamQueryBuilder;
use App\Models\SendProject;
use Illuminate\Support\Facades\DB;

class SendProjectService extends AbstractService
{
    protected $modelClass = SendProject::class;

    protected $modelQueryBuilderClass = SendProjectQueryBuilder::class;

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsWebisteInTables($id)
    {
        $website = $this->findOrFailById($id);

        $campaigns = $website->campaigns->toArray();
        $smtpAccounts = $website->smtpAccounts->toArray();
        $mailTemplates = $website->mailTemplates->toArray();

        if (!empty($campaigns) || !empty($smtpAccounts) || !empty($mailTemplates)) {
            return true;
        }

        return false;
    }

    public function getProjectAssignableForTeam($locationUuids, $departmentUuids, $teamUuid, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $projectRemoves = $this->getProjectAssignedTeam($teamUuid);
        $projectRemoveUuids = $projectRemoves->pluck('uuid')->toArray();

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereNotIn('uuid', $projectRemoveUuids)
            ->where(function ($query) use ($locationUuids, $departmentUuids) {
                $query->whereHas('locations', function ($q) use ($locationUuids) {
                    $q->whereIn('locations.uuid', $locationUuids);
                })
                    ->orWhereHas('departments', function ($q) use ($departmentUuids) {
                        $q->whereIn('departments.uuid', $departmentUuids);
                    });
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getProjectAssignedTeam($teamUuid)
    {

        return $this->model->whereHas('teams', function ($q) use ($teamUuid) {
            $q->where('teams.uuid', $teamUuid);
        })->get();
    }

    public function getMyProjectWithTeams($request, $teams)
    {

        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($query) use ($teams) {
                $query->where('user_uuid', auth()->userId())
                    ->orWhereHas('teams', function ($q) use ($teams) {
                        $q->whereIn('teams.uuid', $teams);
                    });
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function showMyWebsite($id)
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
    public function deleteMyWebsite($id)
    {
        $website = $this->showMyWebsite($id);

        return $this->destroy($website->getKey());
    }

    public function getMyProjectWithDepartment($request, $departmentUuid)
    {
        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereHas('departments', function ($q) use ($departmentUuid) {
                $q->where('departments.uuid', $departmentUuid);
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getProjectScope($request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $query = SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by']);
        if ($request->get('condition') == 'or') {
            if (isset($request->get('type')['departments'])) {
                $departmentUuids = array_values($request->get('type')['departments']);
                $query = $query->orWhereHas('departments', function ($q) use ($departmentUuids) {
                    $q->whereIn('departments.uuid', $departmentUuids);
                });
            }
            if (isset($request->get('type')['teams'])) {
                $teamUuids = array_values($request->get('type')['teams']);
                $query = $query->orWhereHas('teams', function ($q) use ($teamUuids) {
                    $q->whereIn('teams.uuid', $teamUuids);
                });
            }

            if (isset($request->get('type')['locations'])) {
                $locationUuids = array_values($request->get('type')['locations']);
                $query = $query->orWhereHas('locations', function ($q) use ($locationUuids) {
                    $q->whereIn('locations.uuid', $locationUuids);
                });
            }
        } else {
            if (isset($request->get('type')['departments'])) {
                $departmentUuids = array_values($request->get('type')['departments']);
                $query = $query->whereHas('departments', function ($q) use ($departmentUuids) {
                    $q->where(function ($subQuery) use ($departmentUuids) {
                        foreach ($departmentUuids as $departmentUuid) {
                            $subQuery->orWhere('departments.uuid', $departmentUuid);
                        }
                    });
                });
            }
            if (isset($request->get('type')['teams'])) {
                $teamUuids = array_values($request->get('type')['teams']);
                $query = $query->whereHas('teams', function ($q) use ($teamUuids) {
                    $q->where(function ($subQuery) use ($teamUuids) {
                        foreach ($teamUuids as $teamUuid) {
                            $subQuery->orWhere('teams.uuid', $teamUuid);
                        }
                    });
                });
            }
            if (isset($request->get('type')['locations'])) {
                $locationUuids = array_values($request->get('type')['locations']);
                $query = $query->whereHas('locations', function ($q) use ($locationUuids) {
                    $q->where(function ($subQuery) use ($locationUuids) {
                        foreach ($locationUuids as $locationUuid) {
                            $subQuery->orWhere('locations.uuid', $locationUuid);
                        }
                    });
                });
            }
        }

        return $query->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getMyProjectWithDLocation($request, $uuid)
    {
        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereHas('locations', function ($q) use ($uuid) {
                $q->where('locations.uuid', $uuid);
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
