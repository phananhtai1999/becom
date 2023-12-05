<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

    public function showWebsitePageForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [WebsitePage::PENDING_PUBLISH_STATUS, WebsitePage::REJECT_PUBLISH_STATUS, WebsitePage::DRAFT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function totalEditorWebsitePageChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->where('user_uuid', auth()->user()->getKey())
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first()->setAppends([])->toArray();
    }

    public function getWebsitePageChartByDateFormat($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 2, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as reject")
            ->where('user_uuid', auth()->user()->getKey())
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->groupBy('label')
            ->orderBy('label', 'ASC')
            ->get();
    }

    public function editorWebsitePageChart($groupBy, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $currentDate = $startDate->copy();
        $times = [];
        $result = [];

        if ($groupBy == "hour") {
            $dateFormat = "%Y-%m-%d %H:00:00";

            $endDate = $endDate->endOfDay();
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d H:00:00');
                $currentDate = $currentDate->addHour();
            }
        }

        if ($groupBy == "date") {
            $dateFormat = "%Y-%m-%d";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m-d');
                $currentDate = $currentDate->addDay();
            }
        }

        if ($groupBy == "month") {
            $dateFormat = "%Y-%m";
            while ($currentDate <= $endDate) {
                $times[] = $currentDate->format('Y-m');
                $currentDate = $currentDate->addMonth();
            }
        }

        $charts = $this->getWebsitePageChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');
        foreach ($times as $time) {
            $chartByTime = $charts->first(function ($item, $key) use ($time) {
                return $key == $time;
            });

            if ($chartByTime) {
                $result[] = [
                    'label' => $time,
                    'approve' => $chartByTime->approve,
                    'pending' => $chartByTime->pending,
                    'reject' => $chartByTime->reject
                ];
            } else {
                $result [] = [
                    'label' => $time,
                    'approve' => 0,
                    'pending' => 0,
                    'reject' => 0,
                ];
            }
        }

        return $result;
    }

    public function getIsCanUseWebsitePages($request)
    {
        $isCanUseWebsitePages = $this->model
            ->leftJoin('website_website_page', 'website_website_page.website_page_uuid', 'website_pages.uuid')
            ->whereNull('website_website_page.website_page_uuid')
            ->where(function ($query) {
                return $query->where('website_pages.user_uuid', auth()->user()->getKey())
                    ->orWhere('website_pages.is_default', true);
            })->get()->pluck('uuid');
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereIn('uuid', $isCanUseWebsitePages)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }

    public function publicWebsitePageByDomainAndSlug($domainName, $slug)
    {
        $webpage = (new Website())->whereHas('domain', function ($query) use ($domainName) {
            $query->where([
                ['name', $domainName],
                ['verified_at', '!=', null]
            ]);
        })
            ->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
            ->firstOrFail()->websitePagesPublic
            ->where('slug', $slug)
            ->first();

        return $webpage ?? abort(404);
    }

    public function getWebsitePageByDomainAndWebsitePageSlug($domainName, $websitePageSlug)
    {
        $website = (new Website())->whereHas('domain', function ($query) use ($domainName) {
            $query->where([
                ['name', $domainName],
                ['verified_at', '!=', null]
            ]);
        })
            ->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
            ->firstOrFail();

        if ($websitePageSlug) {
            $websitePage = $website->websitePagesPublic()->where(['slug' => $websitePageSlug])->firstOrFail();
        } else {
            $websitePage = $website->websitePagesPublic()->wherePivot('is_homepage', 1)->firstOrFail();
        }

        return $websitePage;
    }

    public function getWebsitePageByDomainAndWebsitePageUuid($domainName, $websitePageUuid)
    {
        $website = (new Website())->whereHas('domain', function ($query) use ($domainName) {
            $query->where([
                ['name', $domainName],
                ['verified_at', '!=', null]
            ]);
        })
            ->firstOrFail();

        if ($websitePageUuid) {
            $websitePage = $website->websitePagesPublic()->where(['uuid' => $websitePageUuid])->firstOrFail();
        } else {
            $websitePage = $website->websitePagesPublic()->wherePivot('is_homepage', 1)->firstOrFail();
        }

        return $websitePage;
    }

    public function getNewsWebsitePagesByDomain($domainName)
    {
        $website = (new Website())->whereHas('domain', function ($query) use ($domainName) {
            $query->where([
                ['name', $domainName],
                ['verified_at', '!=', null]
            ]);
        })
            ->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
            ->firstOrFail();

        return $website->websitePagesPublic()
            ->whereIn('type', [WebsitePage::ARTICLE_CATEGORY_TYPE, WebsitePage::HOME_ARTICLES_TYPE, WebsitePage::ARTICLE_DETAIL_TYPE])
            ->get();
    }

    public function renderContent($websitePage, $article)
    {
        $replaceArticleService = new ReplaceArticleService();

        $searchReplaceMap = $replaceArticleService->searchReplaceMapForArticle($article);
        $websitePage->template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->template);
        $websitePage->template = $replaceArticleService->replaceListArticleForPageHome($websitePage->template);

        return $websitePage;
    }

    public function renderContentForArticleCategory($websitePage, $articleCategory)
    {
        $replaceChildrenCategoryService = new ReplaceChildrenCategoryService();
        $replaceCategoryService = new ReplaceCategoryService();
        $websitePage->template = $replaceChildrenCategoryService->replaceChildrenCategory($websitePage->template, $articleCategory);

        $searchArticleReplaceMap = $replaceCategoryService->searchReplaceMapForCategory($articleCategory);
        $websitePage->template = str_replace(array_keys($searchArticleReplaceMap), $searchArticleReplaceMap, $websitePage->template);

        $replaceArticleService = new ReplaceArticleService();
        $websitePage->template = $replaceArticleService->replaceListArticle($websitePage->template, $articleCategory);

        return $websitePage;
    }

    public function renderContentForHomeArticles($websitePage)
    {
        $replaceArticleService = new ReplaceArticleService();
        $replaceCategoryService = new replaceCategoryService();
        $websitePage->template = $replaceCategoryService->replaceListCategory($websitePage->template);
        $websitePage->template = $replaceArticleService->replaceListArticleForPageHome($websitePage->template);

        return $websitePage;
    }
}
