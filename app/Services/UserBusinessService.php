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
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->whereIn('business_uuid', $id)
            ->whereNotIn('user_uuid', $excludeUser ?? [])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }

    public function listMemberOfAllBusiness($request)
    {
        $request = $this->getIndexRequest($request);
        return $this->modelQueryBuilderClass::searchQuery($request['search'], $request['search_by'])
            ->paginate($request['per_page'], $request['columns'], $request['page_name'], $request['page']);
    }
}
