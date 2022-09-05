<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Email;
use App\Models\QueryBuilders\EmailQueryBuilder;
use phpDocumentor\Reflection\Types\Boolean;

class EmailService extends AbstractService
{
    protected $modelClass = Email::class;

    protected $modelQueryBuilderClass = EmailQueryBuilder::class;

    /**
     * @param $emails
     * @return mixed
     */
    public function getEmailInArray($emails)
    {
        return $this->model->whereIn('email', $emails)->get();
    }

    /**
     * @param $toEmails
     * @param $websiteUuid
     * @return bool
     */
    public function checkEmailValid($toEmails, $websiteUuid)
    {
        foreach ($toEmails as $toEmail){
            $email = $this->model->select('emails.*')
                ->join('website_email', 'emails.uuid', '=', 'website_email.email_uuid')
                ->where([
                    ['website_email.website_uuid', $websiteUuid],
                    ['emails.email', $toEmail]
                ])->first();
            if(!$email){
                return false;
            }
        }
        return true;
    }

    /**
     * @param $websiteUuid
     * @return mixed
     */
    public function getAllEmailsByWebsiteUuid($websiteUuid)
    {
        return $this->model->select('emails.*')
            ->join('website_email', 'emails.uuid', '=', 'website_email.email_uuid')
            ->where('website_email.website_uuid', $websiteUuid)->get();
    }
}
