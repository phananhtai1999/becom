<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\WebsitePage;

class WebsitePageService extends AbstractService
{
    protected $modelClass = WebsitePage::class;

    protected $modelQueryBuilderClass = WebsitePageQueryBuilder::class;

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
