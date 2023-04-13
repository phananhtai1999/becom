<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SendProjectQueryBuilder;
use App\Models\SendProject;

class SendProjectService extends AbstractService
{
    protected $modelClass = SendProject::class;

    protected $modelQueryBuilderClass = SendProjectQueryBuilder::class;

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsWebisteInTables($id)
    {
        $website = $this->findOrFailById($id);

        $campaigns = $website->campaigns->toArray();
        $smtpAccounts = $website->smtpAccounts->toArray();
        $mailTemplates = $website->mailTemplates->toArray();

        if (!empty($campaigns) || !empty($smtpAccounts) || !empty($mailTemplates)) {
            return true;
        }

        return false;
    }
}
