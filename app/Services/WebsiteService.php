<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageCategoryQueryBuilder;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\QueryBuilders\WebsiteQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;

class WebsiteService extends AbstractService
{
    protected $modelClass = Website::class;

    protected $modelQueryBuilderClass = WebsiteQueryBuilder::class;

    public function publicWebsiteByDomainAndPublishStatus($domainName)
    {
        return $this->model->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
            ->whereHas('domain', function ($query) use ($domainName) {
                $query->where([
                    ['name', $domainName],
                    ['verified_at', '!=', null]
                ]);
            })
            ->firstOrFail();
    }
}
