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

    public function getMyProjectWithTeams($request, $teams, $departmentUuid, $location, $businessUuid = null)
    {
        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($query) use ($teams, $departmentUuid, $businessUuid, $location) {
                $query = $query->orWhereHas('teams', function ($q) use ($teams) {
                    $q->whereIn('teams.uuid', $teams);
                })->when($departmentUuid, function ($q, $departmentUuid) {
                    return $q->orWhereHas('departments', function ($q) use ($departmentUuid) {
                        $q->whereIn('departments.uuid', $departmentUuid)
                            ->where(['send_projects.status' => SendProject::STATUS_PROTECTED]);
                    });
                })
                ->when($location, function ($q, $location) {
                    return $q->orWhereHas('locations', function ($q) use ($location) {
                        $q->whereIn('locations.uuid', $location)
                            ->where(['send_projects.status' => SendProject::STATUS_PROTECTED]);
                    });
                });

                if (!empty($businessUuid)) {
                    $query->orWhereHas('business', function ($q) use ($businessUuid) {
                        $q->where('business_managements.uuid', $businessUuid)
                            ->where(['status' => SendProject::STATUS_PROTECTED]);
                    });
                }

            })->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
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

    public function getMyProjectWithDepartment($request, $departmentUuid, $businessUuid, $locationUuid, $teamOfDepartment)
    {
        $indexRequest = $this->getIndexRequest($request);

        $query = SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where (function ($query) use ($departmentUuid, $teamOfDepartment, $businessUuid){
                $query->whereHas('departments', function ($q) use ($departmentUuid) {
                    $q->where('departments.uuid', $departmentUuid);
                })
                    ->orWhereHas('teams', function ($q) use ($teamOfDepartment) {
                        $q->whereIn('teams.uuid', $teamOfDepartment);
                    });

                if (!empty($businessUuid)) {
                    $query->orWhereHas('business', function ($q) use ($businessUuid) {
                        $q->where('business_managements.uuid', $businessUuid)
                            ->where(['status' => SendProject::STATUS_PROTECTED]);
                    });
                }

                if (!empty($locationUuid)) {
                    $query->orWhereHas('locations', function ($q) use ($businessUuid) {
                        $q->where('locations.uuid', $businessUuid)
                            ->where(['status' => SendProject::STATUS_PROTECTED]);
                    });
                }
            });

        return $query->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getProjectScope($request, $business)
    {
        $indexRequest = $this->getIndexRequest($request);
        $query = SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(['business_uuid' => $business->uuid])
        ->where(function ($query) use ($request) {
            if ($request->get('condition') == 'or') {
                if (isset($request->get('type')['departments'])) {
                    $departmentUuids = array_values($request->get('type')['departments']);
                    $query->orWhereHas('departments', function ($q) use ($departmentUuids) {
                        $q->whereIn('departments.uuid', $departmentUuids);
                    });
                }
                if (isset($request->get('type')['teams'])) {
                    $teamUuids = array_values($request->get('type')['teams']);
                    $query->orWhereHas('teams', function ($q) use ($teamUuids) {
                        $q->whereIn('teams.uuid', $teamUuids);
                    });
                }

                if (isset($request->get('type')['locations'])) {
                    $locationUuids = array_values($request->get('type')['locations']);
                    $query->orWhereHas('locations', function ($q) use ($locationUuids) {
                        $q->whereIn('locations.uuid', $locationUuids);
                    });
                }
            } else {
                if (isset($request->get('type')['departments'])) {
                    $departmentUuids = array_values($request->get('type')['departments']);
                    $query->whereHas('departments', function ($q) use ($departmentUuids) {
                        return $q->where(function ($subQuery) use ($departmentUuids) {
                            foreach ($departmentUuids as $departmentUuid) {
                                $subQuery->orWhere('departments.uuid', $departmentUuid);
                            }
                        });
                    });
                }
                if (isset($request->get('type')['teams'])) {
                    $teamUuids = array_values($request->get('type')['teams']);
                    $query->whereHas('teams', function ($q) use ($teamUuids) {
                        return $q->where(function ($subQuery) use ($teamUuids) {
                            foreach ($teamUuids as $teamUuid) {
                                $subQuery->orWhere('teams.uuid', $teamUuid);
                            }
                        });
                    });
                }
                if (isset($request->get('type')['locations'])) {
                    $locationUuids = array_values($request->get('type')['locations']);
                    $query->whereHas('locations', function ($q) use ($locationUuids) {
                        return $q->where(function ($subQuery) use ($locationUuids) {
                            foreach ($locationUuids as $locationUuid) {
                                $subQuery->orWhere('locations.uuid', $locationUuid);
                            }
                        });
                    });
                }
            }
        });


        return $query->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getMyProjectWithDLocation($request, $uuid, $businessUuid, $departmentOfLocation, $teamOfLocation)
    {
        $indexRequest = $this->getIndexRequest($request);

        return SendProjectQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($query) use ($uuid, $departmentOfLocation, $teamOfLocation, $businessUuid) {
                $query = $query->whereHas('locations', function ($q) use ($uuid) {
                    $q->where('locations.uuid', $uuid);
                })
                    ->orWhereHas('departments', function ($q) use ($departmentOfLocation) {
                        $q->whereIn('departments.uuid', $departmentOfLocation);
                    })
                    ->orWhereHas('teams', function ($q) use ($teamOfLocation) {
                        $q->whereIn('teams.uuid', $teamOfLocation);
                    });

                    if(!empty($businessUuid)){
                        $query->orWhereHas('business', function ($q) use ($businessUuid) {
                            $q->where('business_managements.uuid', $businessUuid)
                                ->where(['status' => SendProject::STATUS_PROTECTED]);
                        });
                    }
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
