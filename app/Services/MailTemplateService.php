<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    protected $modelQueryBuilderClass = MailTemplateQueryBuilder::class;

    public function getMailTemplateDefaultWithPagination($perPage, $page, $columns, $pageName)
    {
        return MailTemplateQueryBuilder::initialQuery()->whereNull('website_uuid')
            ->paginate($perPage, $columns, $pageName, $page);
    }
}
