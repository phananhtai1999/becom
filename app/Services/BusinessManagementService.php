<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\BusinessManagement;
use App\Models\QueryBuilders\BusinessManagementQueryBuilder;

class BusinessManagementService extends AbstractService
{
    protected $modelClass = BusinessManagement::class;

    protected $modelQueryBuilderClass = BusinessManagementQueryBuilder::class;

    /**
     * @param $userUuid
     * @param $appId
     * @return mixed
     */
    public function checkBusinessManagementOfUser($userUuid, $appId)
    {
        return $this->model->where([
            ['owner_uuid', $userUuid],
            ['app_id', $appId]
        ])->first();
    }

    /**
     * @param $business
     * @param $domainUuid
     * @return mixed
     */
    public function setDomainDefault($business, $domainUuid)
    {
        return $business->update(['domain_uuid' => $domainUuid]);
    }
}
