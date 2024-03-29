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
        $indexRequest = $this->getIndexRequest($request);

        if ($request->get('sort') == 'num_of_team_member') {
            $result = MyTeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page'])
                ->sortBy('num_of_team_member');
        } elseif ($request->get('sort') == '-num_of_team_member') {
            $result = MyTeamQueryBuilder::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page'])
                ->sortByDesc('num_of_team_member');
        }

        return $this->collectionPagination($result, $indexRequest['per_page'], $indexRequest['page']);
    }

}
