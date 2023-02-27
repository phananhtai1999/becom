<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\SubscriptionPlan;
use App\Models\Website;

class SubscriptionPlanService extends AbstractService
{
    protected $modelClass = SubscriptionPlan::class;

    public function checkExist($request)
    {
        return $this->model->where('platform_package_uuid', $request->get('platform_package_uuid'))
            ->where('duration', $request->get('duration'))
            ->where('duration_type', $request->get('duration_type'))
            ->exists();
    }
}
