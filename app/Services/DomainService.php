<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Domain;
use App\Models\QueryBuilders\DomainQueryBuilder;

class DomainService extends AbstractService
{
    protected $modelClass = Domain::class;

    protected $modelQueryBuilderClass = DomainQueryBuilder::class;

//    /**
//     * @param $domain
//     * @param $business
//     * @return mixed
//     */
//    public function getVerifiedDomainOfBusiness($domain, $business)
//    {
//        return $this->model->where([
//            ['name', $domain],
//            ['business_uuid', $business->uuid],
//            ['verified_at', '!=', null]
//        ])->orWhere([
//            ['name', $domain],
//            ['owner_uuid', $business->owner_uuid],
//            ['verified_at', '!=', null]
//        ])
//            ->orderByDesc('verified_at')->first();
//    }

    /**
     * @param $domain
     * @param $business
     * @return mixed
     */
    public function checkDomainBusinessOfUser($domain, $business)
    {
        return $this->model->where([
            ['name', $domain],
            ['owner_uuid', $business->owner_uuid],
        ]);
    }

    /**
     * @param $domain
     * @param $business
     * @return mixed
     */
    public function checkDomainOfBusiness($domain, $business)
    {
        return $this->model->where([
            ['name', $domain],
            ['owner_uuid', null],
            ['business_uuid', $business->uuid],
        ])->first();
    }

    /**
     * @param $domain
     * @param $business
     * @return mixed
     */
    public function updateOrCreateDomainByBusiness($domain, $business)
    {
//        //Verified Domain
//        $domainVerified = $this->getVerifiedDomainOfBusiness($domain, $business);
//        if ($domainVerified) {
//            return $domainVerified;
//        }

        //Domain of User
        $domainOfUser = $this->checkDomainBusinessOfUser($domain, $business)->first();
        if ($domainOfUser) {
            $this->checkDomainBusinessOfUser($domain, $business)->update(['business_uuid' => $business->uuid]);

            return $domainOfUser;
        }

        //Domain of Business
        $domainOfBusiness = $this->checkDomainOfBusiness($domain, $business);
        if ($domainOfBusiness) {

            return $domainOfBusiness;
        }

        //Create new domain
        return $this->model->create([
            'name' => $domain,
            'owner_uuid' => null,
            'business_uuid' => $business->uuid,
            'verified_at' => null
        ]);
    }

//    /**
//     * @param $business
//     * @param $domain
//     * @return mixed
//     */
//    public function setVerifiedDomainForBusiness($business, $domain)
//    {
//        $domainOfUser = $this->checkDomainBusinessOfUser($domain->name, $business)->first();
//        if ($domainOfUser) {
//            $this->checkDomainBusinessOfUser($domain->name, $business)->update(['business_uuid' => $business->uuid]);
//
//            return $domainOfUser;
//        }
//        $getDomain = $this->model->where([
//            ['business_uuid', $business->uuid],
//            ['uuid', '!=', $domain->uuid]
//        ]);
//
//        return $getDomain->update([
//            'verified_at' => null
//        ]);
//    }
}
