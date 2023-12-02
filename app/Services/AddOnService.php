<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOn;
use App\Models\BusinessManagement;
use App\Models\QueryBuilders\AddOnQueryBuilder;
use App\Models\Team;
use App\Models\UserAddOn;

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

    public function getAddOnsByBusiness($request, $businessUuid, $excludeTeamUuid = null) {
        $business = BusinessManagement::findOrFail($businessUuid);
        if (!empty($excludeTeamUuid)) {
            $teamExclude = Team::findOrFail($excludeTeamUuid);
            $excludeAddOn = $teamExclude->addOns()->pluck('uuid')->toArray();
        }
        $userAddOns = UserAddOn::select(['user_uuid', 'add_on_subscription_plan_uuid'])
            ->where(['user_uuid' => $business->owner_uuid])
            ->distinct()->get();
        $addOnUuids = [];
        foreach ($userAddOns as $userAddOn) {
            if ($userAddOn->addOnSubscriptionPlan) {
                $addOnUuids[] = $userAddOn->addOnSubscriptionPlan->addOn->uuid;
            }
        }
        $filteredAddOnUuids = array_diff($addOnUuids, $excludeAddOn ?? []);

        $indexRequest = $this->getIndexRequest($request);
        if($filteredAddOnUuids) {
            return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
                ->whereIn('uuid', $filteredAddOnUuids)
                ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
        }

        return [];
    }
}
