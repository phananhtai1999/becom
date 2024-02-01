<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SendProjectQueryBuilder;
use App\Models\SubscriptionPlan;
use App\Models\SendProject;

class SubscriptionPlanService extends AbstractService
{
    protected $modelClass = SubscriptionPlan::class;
    protected $modelQueryBuilderClass = SubscriptionPlanQueryBuilder::class;
    public function checkExist($request)
    {
        return $this->model->where('app_uuid', $request->get('app_uuid'))
            ->where('duration', $request->get('duration'))
            ->where('duration_type', $request->get('duration_type'))
            ->exists();
    }
}
