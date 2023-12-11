<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PayoutMethod;
use App\Models\QueryBuilders\PayoutInformationQueryBuilder;

class PayoutInformationService extends AbstractService
{
    protected $modelClass = PayoutMethod::class;

    protected $modelQueryBuilderClass = PayoutInformationQueryBuilder::class;

    public function getDefault()
    {
        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
            'is_default' => true
        ])->first();
    }

    public function getMyPayoutInformation()
    {
        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ])
            ->orderBy('is_default', 'DESC')->get();
    }
}
