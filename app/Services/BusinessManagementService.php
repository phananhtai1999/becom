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
     * @return mixed
     */
    public function checkBusinessManagementOfUser($userUuid)
    {
        return $this->model->where('owner_uuid', $userUuid)->first();
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
