<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOnSubscriptionPlan;
use App\Models\QueryBuilders\AddOnSubscriptionPlanQueryBuilder;

class AddOnSubscriptionPlanService extends AbstractService
{
    protected $modelClass = AddOnSubscriptionPlan::class;
    protected $modelQueryBuilderClass = AddOnSubscriptionPlanQueryBuilder::class;
    public function checkExist($request)
    {
        return $this->model->where('add_on_uuid', $request->get('add_on_uuid'))
            ->where('duration_type', $request->get('duration_type'))
            ->exists();
    }
}
