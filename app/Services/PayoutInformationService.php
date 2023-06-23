<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PayoutInformation;
use App\Models\QueryBuilders\PayoutInformationQueryBuilder;

class PayoutInformationService extends AbstractService
{
    protected $modelClass = PayoutInformation::class;

    protected $modelQueryBuilderClass = PayoutInformationQueryBuilder::class;

    public function getDefault()
    {
        return $this->model->where(['user_uuid' => auth()->user()->getkey(), 'is_default' => true])->first();
    }

    public function getMyPayoutInformation()
    {
        return $this->model->where(['user_uuid' => auth()->user()->getkey()])
            ->orderBy('is_default', 'DESC')->get();
    }
}
