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
        return $this->model->orderBy('number_of_references', 'ASC')->first();
    }

    public function getPartnerLevelByPartner($partner)
    {
        if ($partner->code) {
            $numberCustomers = (new PartnerUserService())->customersPartner($partner->code)->count();
            $partnerLevelByNumber = $this->model->where('number_of_references', '<=' , $numberCustomers)
                ->orderBy('number_of_references', 'DESC')->first();
            if ($partnerLevelByNumber) {
                return $partnerLevelByNumber;
            }
        }
        return $this->getDefaultPartnerLevel();
    }
}
