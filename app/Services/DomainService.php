<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Domain;
use App\Models\QueryBuilders\DomainQueryBuilder;

class DomainService extends AbstractService
{
    protected $modelClass = Domain::class;

    protected $modelQueryBuilderClass = DomainQueryBuilder::class;

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

    /**
     * @param $domainVerified
     * @return mixed
     */
    public function updateDomainVerified($domainVerified)
    {
        $this->model->where([
            ['name', $domainVerified->domain->name],
            ['uuid', '!=', $domainVerified->domain_uuid],
            ['verified_at', '!=', null]
        ])->update(['verified_at' => null]);

        return $domainVerified->domain->update([
            'verified_at' => $domainVerified->verified_at,
            'owner_uuid' => auth()->user()->getkey()
        ]);
    }
}
