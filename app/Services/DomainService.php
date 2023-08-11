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
     * @param $businessUuid
     * @param $configMailboxMx
     * @param $configMailboxDmarc
     * @param $configMailboxDkim
     * @return mixed
     */
    public function updateActiveMailboxStatusDomain($businessUuid, $configMailboxMx, $configMailboxDmarc, $configMailboxDkim)
    {
        //Get all where domains
        $domains = $this->findAllWhere([
            ['business_uuid', $businessUuid],
            ['active_mailbox', false]
        ]);

        $activeMailboxMxStatus = !empty($configMailboxMx->value);
        $activeMailboxDmarcStatus = !empty($configMailboxDmarc->value);
        $activeMailboxDkimStatus = !empty($configMailboxDkim->value);

        return $domains->each(function ($item) use (
            $configMailboxMx, $configMailboxDmarc, $configMailboxDkim,
            $activeMailboxMxStatus, $activeMailboxDmarcStatus, $activeMailboxDkimStatus
        ) {
            //Check record domain
            $checkRecordsDomainExistOrNot = $this->checkRecordsDomainExistOrNot($item, $configMailboxMx, $configMailboxDmarc, $configMailboxDkim);
            $item->update([
                'active_mailbox' => $checkRecordsDomainExistOrNot['status'],
                'active_mailbox_status' => [
                    array_merge($activeMailboxMxStatus ? $configMailboxMx->value : [], ['status' => $checkRecordsDomainExistOrNot['mx_status']]),
                    array_merge($activeMailboxDmarcStatus ? $configMailboxDmarc->value : [], ['status' => $checkRecordsDomainExistOrNot['dmarc_status']]),
                    array_merge($activeMailboxDkimStatus ? $configMailboxDkim->value : [], ['status' => $checkRecordsDomainExistOrNot['dkim_status']]),
                ]
            ]);
        });
    }

    /**
     * @param $domain
     * @param $configMailboxMx
     * @param $configMailboxDmarc
     * @param $configMailboxDkim
     * @return array
     */
    public function checkRecordsDomainExistOrNot($domain, $configMailboxMx, $configMailboxDmarc, $configMailboxDkim)
    {
        $dnsMxRecords = dns_get_record($domain->name, DNS_MX);
        $dnsTxtRecords = dns_get_record($domain->name, DNS_TXT);
        $mxStatus = false;
        $dmarcStatus = false;
        $dkimStatus = false;

        //TXT record
        foreach ($dnsTxtRecords as $record) {
            if (!empty($configMailboxDmarc->value['value']) &&
                $record['txt'] === $configMailboxDmarc->value['value']) {
                $dmarcStatus = true;
            } elseif (!empty($configMailboxDkim->value['value']) &&
                $record['txt'] === $configMailboxDkim->value['value']) {
                $dkimStatus = true;
            }
        }

        //MX record
        foreach ($dnsMxRecords as $record) {
            if (!empty($configMailboxMx->value['value']) &&
                $record['target'] === $configMailboxMx->value['value']) {
                $mxStatus = true;
            }
        }

        if ($mxStatus && $dkimStatus && $dmarcStatus) {
            return [
                'status' => true,
                'mx_status' => $mxStatus,
                'dmarc_status' => $dmarcStatus,
                'dkim_status' => $dkimStatus,
            ];
        } else {
            return [
                'status' => false,
                'mx_status' => $mxStatus,
                'dmarc_status' => $dmarcStatus,
                'dkim_status' => $dkimStatus,
            ];
        }
    }
}
