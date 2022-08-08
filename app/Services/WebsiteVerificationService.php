<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsiteVerificationQueryBuilder;
use App\Models\WebsiteVerification;
use Carbon\Carbon;
use Illuminate\Support\Str;

class WebsiteVerificationService extends AbstractService
{
    protected $modelClass = WebsiteVerification::class;

    protected $modelQueryBuilderClass = WebsiteVerificationQueryBuilder::class;

    /**
     * @param $websiteVerification
     * @return mixed
     */
    public function setVerified($websiteVerification)
    {
        $this->update($websiteVerification, [
            'verified_at' => Carbon::now()
        ]);

        return $websiteVerification;
    }

    /**
     * @param $websiteVerification
     * @return mixed
     */
    public function setNotVerified($websiteVerification)
    {
        $this->update($websiteVerification, [
            'verified_at' => null
        ]);

        return $websiteVerification;
    }

    /**
     * @return false|string
     */
    public function generateToken()
    {
        return config('app.name') . '=' . hash_hmac('sha256', Str::uuid(), config('app.key'));
    }

    /**
     * @param $websiteUuid
     * @return mixed
     */
    public function firstOrCreateByWebsiteUuid($websiteUuid)
    {
        return $this->model->firstOrCreate(
            ['website_uuid' => $websiteUuid],
            ['token' => $this->generateToken()]
        );

    }

    /**
     * @param $websiteUuid
     * @return mixed
     */
    public function verifyByDnsRecord($websiteUuid)
    {
        $record = $this->firstOrCreateByWebsiteUuid($websiteUuid);
        if ($this->tokenExists($record->website->domain, $record->token)) {

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

    /**
     * @param $websiteUuid
     * @return mixed
     */
    public function verifyByHtmlTag($websiteUuid)
    {
        $record = $this->firstOrCreateByWebsiteUuid($websiteUuid);
        $domainToken = $this->getMetaTagToken($record->website->domain);

        if ($record->token == $domainToken) {
            return $this->setVerified($record);
        } else {
            return $this->setNotVerified($record);
        }
    }

    /**
     * @param $url
     * @return mixed|string
     */
    public function getMetaTagToken($url)
    {
        $metaTag = get_meta_tags($url);
        $metaTagName = config('app.name') . '-verify-tag';

        if (!isset($metaTag[$metaTagName])) {
            return '';
        }

        return $metaTag[$metaTagName];
    }

    /**
     * @param $websiteUuid
     * @return mixed
     */
    public function verifyByHtmlFile($websiteUuid)
    {
        $record = $this->firstOrCreateByWebsiteUuid($websiteUuid);
        $verificationToken = $record->token;
        $domainToken = $this->getHtmlFileToken($record->website->domain);

        if ($domainToken == $verificationToken) {
            return $this->setVerified($record);
        } else {
            return $this->setNotVerified($record);
        }

    }

    /**
     * @param $url
     * @return false|string
     */
    public function getHtmlFileToken($url)
    {
        $verificationFileName = app(FileVerificationService::class)->verificationFileName();
        $urlFile = $url . '/' . $verificationFileName;
        $urlFileExists = $this->urlFileExists($urlFile);
        if ($urlFileExists) {
            $fileGetToken = file_get_contents($urlFile);
            return $fileGetToken;
        }

        return $urlFile;
    }

    /**
     * @param $urlFile
     * @return bool
     */
    public function urlFileExists($urlFile)
    {
        return str_contains(get_headers($urlFile)[0], "200 OK");
    }

}
