<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    protected $modelQueryBuilderClass = MailTemplateQueryBuilder::class;

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMailTemplateDefaultWithPagination($perPage, $page, $columns, $pageName)
    {
        return MailTemplateQueryBuilder::initialQuery()->whereNull('website_uuid')
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function indexMailtemplateByPublishStatus($publishStatus, $perPage, $columns, $pageName, $page)
    {
        return MailTemplateQueryBuilder::initialQuery()->where('publish_status', $publishStatus)
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findMailTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }
}
