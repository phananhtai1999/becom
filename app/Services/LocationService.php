<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Location;
use App\Models\QueryBuilders\LocationQueryBuilder;

class LocationService extends AbstractService
{
    protected $modelClass = Location::class;

    protected $modelQueryBuilderClass = LocationQueryBuilder::class;

    public function getByProject($id)
    {
        return $this->model->whereHas('sendProjects', function ($query) use ($id) {
            $query->where('send_projects.uuid', $id);
        })->get();
    }

    public function getLocationsAssignable($businessUuid, $projectUuid, $request)
    {
        $indexRequest = $this->getIndexRequest($request);
        $locationRemoves = $this->getLocationsAssignedProject($projectUuid);
        $locationRemoveUuids = $locationRemoves->pluck('uuid')->toArray();

        return LocationQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereNotIn('uuid', $locationRemoveUuids)
            ->where('business_uuid', $businessUuid)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function getLocationsAssignedProject($projectUuid)
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

    public function getMyIndex($request)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where(function ($q) {
                $q->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()],
                ])->orwhere('manager_uuid', auth()->userId());
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
