<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SendProjectQueryBuilder;
use App\Models\QueryBuilders\TeamQueryBuilder;
use App\Models\SendProject;

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

    public function getProjectAssignedTeam($teamUuid) {

        return $this->model->whereHas('teams', function ($q) use ($teamUuid) {
            $q->where('teams.uuid', $teamUuid);
        })->get();
    }

    public function getMyProjectWithTeams($request, $teams)
    {

        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($query) use ($teams) {
                $query->where('user_uuid', auth()->user()->getKey())
                    ->orWhereHas('teams', function ($q) use ($teams) {
                        $q->whereIn('teams.uuid', $teams);
                    });
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
