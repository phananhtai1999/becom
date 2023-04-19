<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyTeamQueryBuilder;
use App\Models\Team;

class MyTeamService extends AbstractService
{
    protected $modelClass = Team::class;

    protected $modelQueryBuilderClass = MyTeamQueryBuilder::class;

    public function sortByNumOfTeamMember($request)
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $columns = $request->get('columns', '*');
        $pageName = $request->get('page_name', 'page');
        $search = $request->get('search', '');
        $searchBy = $request->get('search_by', '');
        if ($request->get('sort') == 'num_of_team_member') {
            $result = MyTeamQueryBuilder::searchQuery($search, $searchBy)
                ->paginate($perPage, $columns, $pageName, $page)
                ->sortBy('num_of_team_member');
        } elseif ($request->get('sort') == '-num_of_team_member') {
            $result = MyTeamQueryBuilder::searchQuery($search, $searchBy)
                ->paginate($perPage, $columns, $pageName, $page)
                ->sortByDesc('num_of_team_member');
        }

        return $this->collectionPagination($result, $perPage, $page);
    }

}
