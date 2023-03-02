<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SectionTemplateQueryBuilder;
use App\Models\SectionTemplate;

class SectionTemplateService extends AbstractService
{
    protected $modelClass = SectionTemplate::class;

    protected $modelQueryBuilderClass = SectionTemplateQueryBuilder::class;

    /**
     * @param $publishStatus
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function indexSectionTemplateByPublishStatus($publishStatus, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        return SectionTemplateQueryBuilder::searchQuery($search, $searchBy)->where('publish_status', $publishStatus)
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findSectionTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }
}