<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserTeamQueryBuilder;
use App\Models\UserTeam;

class UserTeamService extends AbstractService
{
    protected $modelClass = UserTeam::class;

    protected $modelQueryBuilderClass = UserTeamQueryBuilder::class;

    public function listTeamMember($id, $request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->where('team_uuid', $id)
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }

    public function listTeamMemberOfAllTeam($request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }
}
