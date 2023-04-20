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

    public function getPartnerLevelCurrentByPartner($partnerCode)
    {
        $numberCustomers = (new PartnerUserService())->numberCustomerPartnerByMonthCurrent($partnerCode)->count();
        return $this->getPartnerLevelByNumberCustomer($numberCustomers);
    }

    public function getPartnerLevelOfPartnerByMontYear($partnerCode, $month, $year)
    {
        $numberCustomers = (new PartnerUserService())->numberCustomerPartnerByMonthYear($partnerCode, $month, $year)->count();
        return $this->getPartnerLevelByNumberCustomer($numberCustomers);
    }
}
