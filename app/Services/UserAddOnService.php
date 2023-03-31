<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserAddOn;
use Carbon\Carbon;

class UserAddOnService extends AbstractService
{
    protected $modelClass = UserAddOn::class;

    /**
     * @param $addOnSubscriptionPlanUuid
     * @return mixed
     */
    public function checkPurchasedAddOn($addOnSubscriptionPlanUuid)
    {
        return $this->model->where([
            'user_uuid' => auth()->user()->getKey(),
            'add_on_subscription_plan_uuid' => $addOnSubscriptionPlanUuid,
        ])->where('expiration_date', '>', Carbon::now())->get();
    }

}
