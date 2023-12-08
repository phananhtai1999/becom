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

    public function getDefaultWebsiteForAdmin(\App\Http\Requests\IndexRequest $request)
    {
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->where('domain_uuid', null)
            ->where(function ($q) {
                return $q->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
                    ->orWhere('publish_status', Website::BLOCKED_PUBLISH_STATUS);
            })
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function showCopyWebsiteByUuid($uuid)
    {
        return $this->model->where('uuid', $uuid)
            ->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->user()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhere(function ($q) {
                        return $q->where([
                            'domain_uuid' => null,
                            'publish_status' => Website::PUBLISHED_PUBLISH_STATUS,
                        ]);
                    });
            })->firstOrFail();
    }
}
