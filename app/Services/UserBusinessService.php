<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\BusinessTeamQueryBuilder;
use App\Models\UserBusiness;
use App\Models\UserTeam;

class UserBusinessService extends AbstractService
{
    protected $modelClass = UserBusiness::class;

    protected $modelQueryBuilderClass = BusinessTeamQueryBuilder::class;

    public function listTeamMember($id, $request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->whereIn('team_uuid', $id)
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }

    public function listTeamMemberOfAllTeam($request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }
}
