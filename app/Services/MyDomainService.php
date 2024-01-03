<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Domain;
use App\Models\QueryBuilders\MyDomainQueryBuilder;

class MyDomainService extends AbstractService
{
    protected $modelClass = Domain::class;

    protected $modelQueryBuilderClass = MyDomainQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyDomain($id)
    {
        return  $this->findOneWhereOrFail([
            ['owner_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyDomain($id)
    {
        $website = $this->showMyDomain($id);

        return $this->destroy($website->getKey());
    }

    /**
     * @param $domain
     * @return mixed
     */
    public function checkDomainBusinessOfCurrentUser($domain)
    {
        return $this->model->where([
            ['name', $domain],
            ['owner_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
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
        $domainOfCurrentUser = $this->checkDomainBusinessOfCurrentUser($domain)->first();
        if ($domainOfCurrentUser) {
            $this->checkDomainBusinessOfCurrentUser($domain)->update(['business_uuid' => $business->uuid]);

            return $domainOfCurrentUser;
        }

        //Domain of Business
        $domainOfBusiness = $this->checkDomainOfBusiness($domain, $business);
        if ($domainOfBusiness) {

            return $domainOfBusiness;
        }

        return $this->model->create([
            'name' => $domain,
            'active_mailbox' => false,
            'owner_uuid' => null,
            'business_uuid' => $business->uuid,
            'verified_at' => null
        ]);
    }
}
