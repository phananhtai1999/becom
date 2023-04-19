<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerLevel;
use App\Models\QueryBuilders\PartnerLevelQueryBuilder;

class PartnerLevelService extends AbstractService
{
    protected $modelClass = PartnerLevel::class;

    protected $modelQueryBuilderClass = PartnerLevelQueryBuilder::class;

    public function getDefaultPartnerLevel()
    {
        return $this->model->orderBy('number_of_customers', 'ASC')->first();
    }

    public function getPartnerLevelMax()
    {
        return $this->model->orderBy('number_of_customers', 'DESC')->first();
    }

    public function getPartnerLevelByNumberCustomer($numberCustomers)
    {
        $partnerLevelByNumber = $this->model->where('number_of_customers', '>=' , $numberCustomers)
            ->orderBy('number_of_customers', 'ASC')->first();
        if (!$partnerLevelByNumber) {
            return $this->getPartnerLevelMax();
        }
        return $partnerLevelByNumber;
    }

    public function getPartnerLevelByPartner($partner)
    {
        if ($partner->code) {
            $numberCustomers = (new PartnerUserService())->numberCustomerPartnerInMonth($partner->code)->count();
            return $this->getPartnerLevelByNumberCustomer($numberCustomers);
        }
        return $this->getDefaultPartnerLevel();
    }
}
