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

    /**
     * @param $domainUuid
     * @return mixed
     */
    public function findDomainByUuid($domainUuid)
    {
        return $this->findOneById($domainUuid);
    }

    /**
     * @param $configMailboxMx
     * @param $configMailboxDmarc
     * @param $configMailboxDkim
     * @return mixed
     */
    public function updateActiveMailboxStatusDomain($configMailboxMx, $configMailboxDmarc, $configMailboxDkim)
    {
        $activeMailbox = !empty($configMailboxMx->value['value']) &&
            !empty($configMailboxDmarc->value['value']) &&
            !empty($configMailboxDkim->value['value']) &&
            config('mailbox.mailbox_mx_domain') == !empty($configMailboxMx->value['record']);
        $activeMailboxMxStatus = !empty($configMailboxMx->value['value']) &&
            config('mailbox.mailbox_mx_domain') == !empty($configMailboxMx->value['record']);
        $activeMailboxDmarcStatus = !empty($configMailboxDmarc->value['value']);
        $activeMailboxDkimStatus = !empty($configMailboxDkim->value['value']);

        $domains = $this->model->all();

        return $domains->each(function ($item) use (
            $activeMailbox, $configMailboxMx,
            $configMailboxDmarc, $configMailboxDkim, $activeMailboxMxStatus,
            $activeMailboxDmarcStatus, $activeMailboxDkimStatus
        ) {
            $item->update([
                'active_mailbox' => $activeMailbox,
                'active_mailbox_status' => [
                    array_merge($activeMailboxMxStatus ? $configMailboxMx->value : [], ['status' => $activeMailboxMxStatus]),
                    array_merge($activeMailboxDmarcStatus ? $configMailboxDmarc->value : [], ['status' => $activeMailboxDmarcStatus]),
                    array_merge($activeMailboxDkimStatus ? $configMailboxDkim->value : [], ['status' => $activeMailboxDkimStatus]),
                ]
            ]);
        });
    }
}
