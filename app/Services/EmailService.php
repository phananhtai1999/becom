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
            $email = $this->findOneWhere([
                ['email', $toEmail],
                ['website_uuid', $websiteUuid]
            ]);
           if(!$email){
               return false;
           }
        }

        return true;
    }
}
