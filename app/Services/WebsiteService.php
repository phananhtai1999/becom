<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\Website;

class WebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = WebsiteQueryBuilder::class;

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
