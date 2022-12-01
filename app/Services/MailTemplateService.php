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
     * @param $id
     * @return bool
     */
    public function checkExistsMailTemplateInTables($id)
    {
        $mailTemplate = $this->findOrFailById($id);

        $campaigns = $mailTemplate->campaigns->toArray();

        if (!empty($campaigns)) {
            return true;
        }

        return false;
    }

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getMailTemplateDefaultWithPagination($publishedStatus, $perPage, $page, $columns, $pageName)
    {
        return MailTemplateQueryBuilder::initialQuery()
            ->where('publish_status', $publishedStatus)
            ->whereNull('website_uuid')
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
