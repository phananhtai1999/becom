<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\DomainVerification;
use App\Models\QueryBuilders\DomainVerificationQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DomainVerificationService extends AbstractService
{
    protected $modelClass = DomainVerification::class;

    protected $modelQueryBuilderClass = DomainVerificationQueryBuilder::class;

    /**
     * @param $domainVerification
     * @return mixed
     */
    public function setVerified($domainVerification)
    {
        $this->update($domainVerification, [
            'verified_at' => Carbon::now()
        ]);

        return $domainVerification;
    }

    /**
     * @param $domainVerification
     * @return mixed
     */
    public function setNotVerified($domainVerification)
    {
        $this->update($domainVerification, [
            'verified_at' => null
        ]);

        return $domainVerification;
    }

    /**
     * @return string
     */
    public function generateToken()
    {
        return config('app.name') . '=' . hash_hmac('sha256', Str::uuid(), config('app.key'));
    }

    /**
     * @param $domainUuid
     * @return mixed
     */
    public function firstOrCreateByDomainUuid($domainUuid)
    {
        return $this->model->firstOrCreate(
            ['domain_uuid' => $domainUuid],
            ['token' => $this->generateToken()]
        );

    }

    /**
     * @param $domainUuid
     * @return mixed
     */
    public function verifyByDnsRecord($domainUuid)
    {
        $record = $this->firstOrCreateByDomainUuid($domainUuid);
        if ($this->tokenExists($record->domain->name, $record->token)) {

            return $this->setVerified($record);
        } else {

            return $this->setNotVerified($record);
        }
    }

    /**
     * @param $url
     * @return array
     */
    public function getTxtRecordValue($url)
    {
        $dnsRecords = dns_get_record($url, DNS_TXT);
        if (count($dnsRecords) > 0) {
            foreach ($dnsRecords as $record) {
                $txtRecord[] = $record['txt'];
            }

            return $txtRecord;
        }

        return [];
    }

    /**
     * @param $url
     * @param $token
     * @return bool
     */
    public function tokenExists($url, $token)
    {
        $txtRecordValues = $this->getTxtRecordValue($url);
        $verificationValue = $token;

        return in_array($verificationValue, $txtRecordValues);
    }
}
