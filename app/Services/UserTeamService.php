<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserTeamQueryBuilder;
use App\Models\UserTeam;

class UserTeamService extends AbstractService
{
    protected $modelClass = UserTeam::class;

    protected $modelQueryBuilderClass = UserTeamQueryBuilder::class;

    public function listBusinessMember($id, $request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->whereIn('team_uuid', $id)
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }

    public function listMemberOfBusiness($request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }
}
