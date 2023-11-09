<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOn;
use App\Models\QueryBuilders\AddOnQueryBuilder;
use App\Models\Team;

class AddOnService extends AbstractService
{
    protected $modelClass = AddOn::class;

    protected $modelQueryBuilderClass = AddOnQueryBuilder::class;

    public function getAddOnsByTeam($request, $teamUuid) {
        $indexRequest = $this->getIndexRequest($request);
        $team = Team::findOrFail($teamUuid);
        if($team->addOns) {
            return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->whereIn('uuid', $team->addOns->pluck('uuid')->toArray())
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
        }

        return $team->addOns;
    }
}
