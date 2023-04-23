<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\PartnerLevel;
use App\Models\QueryBuilders\PartnerLevelQueryBuilder;
use Carbon\Carbon;

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

    public function checkTodayEndOfMonth()
    {
        $today = Carbon::today();
        return $today->copy()->endOfMonth()->startOfDay()->eq($today);
    }

    public function getCommissionByTimeOfPartner($time, $partnerCode)
    {
        $toDay = Carbon::today();

        if (!$this->checkTodayEndOfMonth() && ($time->format('Y-m') === $toDay->format('Y-m'))) {
            $subMonth = $time->copy()->subMonth();
            $commissionByTime = $this->getPartnerLevelOfPartnerByMontYear($partnerCode, $subMonth->month, $subMonth->year)->commission;
        }else{
            $commissionByTime = $this->getPartnerLevelOfPartnerByMontYear($partnerCode, $time->month, $time->year)->commission;
        }

        return $commissionByTime;
    }
}
