<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\AddOnSubscriptionHistory;
use App\Models\QueryBuilders\AddOnSubscriptionHistoryQueryBuilder;

class AddOnSubscriptionHistoryService extends AbstractService
{

    protected $modelClass = AddOnSubscriptionHistory::class;

    protected $modelQueryBuilderClass = AddOnSubscriptionHistoryQueryBuilder::class;

    public function myAddOnSubscriptionHistories()
    {

        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ])->orderBy('uuid', 'DESC')->get();
    }

    public function currentSubscriptionHistory()
    {

        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ])->orderBy('uuid', 'DESC')->first();
    }

    public function findByLog($log)
    {
        return $this->model->where('logs->id', $log)->first();
    }

    /**
     * @return mixed
     */
    public function getAddOnSubscriptionHistoryOfCurrentUser()
    {
        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId()
        ])->first();
    }
}
