<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\WebsitePage;

class WebsitePageService extends AbstractService
{
    protected $modelClass = WebsitePage::class;

    protected $modelQueryBuilderClass = WebsitePageQueryBuilder::class;

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
    public function indexWebsitePageByPublishStatus($publishStatus, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        return WebsitePageQueryBuilder::searchQuery($search, $searchBy)->where('publish_status', $publishStatus)
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findWebsitePageByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }
}
