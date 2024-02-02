<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserBusinessQueryBuilder;
use App\Models\Team;
use App\Models\UserBusiness;
use App\Models\UserTeam;

class UserBusinessService extends AbstractService
{
    protected $modelClass = UserBusiness::class;

    protected $modelQueryBuilderClass = UserBusinessQueryBuilder::class;

    public function listBusinessMember($id, $request, $excludeTeamUuid = null)
    {
        if (!empty($excludeTeamUuid)) {
            $teamExclude = Team::findOrFail($excludeTeamUuid);
            $excludeUser = $teamExclude->users->pluck('uuid')->toArray();
        }
        $indexRequest = $this->getIndexRequest($request);
        $query = UserBusinessQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
        ->whereIn('business_uuid', $id)
        ->whereNotIn('user_uuid', $excludeUser ?? []);
        $teamUuids = [];
        if (isset($request->get('type')['departments'])) {
            $departmentUuids = array_values($request->get('type')['departments']);
            $departmentService = new DepartmentService();
            $departments = $departmentService->findAllWhereIn('uuid', $departmentUuids);
            if ($departments->isNotEmpty()) {
                foreach ($departments as $department) {
                    if (!empty($department->teams->toArray())) {
                        $teamUuids = array_merge($teamUuids, $department->teams->pluck('uuid')->toArray());
                    }
                }
            }
        }
        if (isset($request->get('type')['locations'])) {
            $locationUuids = array_values($request->get('type')['locations']);
            $locationService = new LocationService();
            $locations = $locationService->findAllWhereIn('uuid', $locationUuids);
            if ($locations->isNotEmpty()) {
                foreach ($locations as $location) {
                    if ($location->departments->isNotEmpty()) {
                        foreach ($location->departments as $department) {
                            if ($department->teams->isNotEmpty()) {
                                $teamUuids = array_merge($teamUuids, $department->teams->pluck('uuid')->toArray());
                            }

                        }
                    }
                }
            }
        }
        if (isset($request->get('type')['teams'])) {
            $teamUuids = array_merge($teamUuids, array_values($request->get('type')['teams']));
        }
        $query = $query->where(function ($query) use ($request) {
            if (!empty($teamUuids)) {
                if ($request->get('condition') == 'or') {
                    $query->whereHas('user', function ($q) use ($teamUuids) {
                        $q->whereHas('teams', function ($q) use ($teamUuids) {
                            $q->whereIn('teams.uuid', $teamUuids);
                        });
                    });
                } else {
                    $query->whereHas('user', function ($q) use ($teamUuids) {
                        $q->whereHas('teams', function ($q) use ($teamUuids) {
                            $q->where(function ($subQuery) use ($teamUuids) {
                                foreach ($teamUuids as $teamUuid) {
                                    $subQuery->where('teams.uuid', $teamUuid);
                                }
                            });
                        });
                    });
                }
            }
        });

        return $query->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function listMemberOfAllBusiness($request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }
}
