<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use App\Models\QueryBuilders\TeamQueryBuilder;
use App\Models\Team;

class TeamService extends AbstractService
{
    protected $modelClass = Team::class;

    protected $modelQueryBuilderClass = TeamQueryBuilder::class;

    public function sortByNumOfTeamMember($request)
    {
        $indexRequest = $this->getIndexRequest($request);
        if ($request->get('sort') == 'num_of_team_member') {
            $result = TeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page'])
                ->sortBy('num_of_team_member');
        } elseif ($request->get('sort') == '-num_of_team_member') {
            $result = TeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page'])
                ->sortByDesc('num_of_team_member');
        }

        return $this->collectionPagination($result, $indexRequest['per_page'], $indexRequest['page']);
    }

    public function getTeamsByIds($teamUuids, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        return TeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereIn('uuid', $teamUuids)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getTeamsAssignable($locationUuids, $departmentUuids, $projectUuid, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $teamRemoves = $this->getTeamsAssignedProject($projectUuid);
        $teamRemoveUuids = $teamRemoves->pluck('uuid')->toArray();

        return TeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereNotIn('uuid', $teamRemoveUuids)
            ->where(function ($query) use ($locationUuids, $departmentUuids) {
                $query->whereHas('location', function ($q) use ($locationUuids) {
                    $q->whereIn('uuid', $locationUuids);
                })
                    ->orWhereHas('department', function ($q) use ($departmentUuids) {
                        $q->whereIn('uuid', $departmentUuids);
                    });
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getTeamsAssignedProject($projectUuid) {

        return $this->model->whereHas('sendProjects', function ($q) use ($projectUuid) {
            $q->where('send_projects.uuid', $projectUuid);
        })->get();
    }
}
