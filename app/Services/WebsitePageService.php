<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\WebsitePageQueryBuilder;
use App\Models\Website;
use App\Models\WebsitePage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
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
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
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
                return $query->where([
                    ['website_pages.user_uuid', auth()->userId()],
                    ['website_pages.app_id', auth()->appId()]
                ])
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
            $websitePage = $website->websitePages()->where(['slug' => $websitePageSlug])->firstOrFail();
        } else {
            $websitePage = $website->websitePages()->wherePivot('is_homepage', 1)->firstOrFail();
        }

        return $websitePage;
    }

    public function getWebsitePageByWebsiteAndWebsitePageUuid($websiteUuid, $websitePageUuid)
    {
        $website = (new Website())->where('uuid', $websiteUuid)->firstOrFail();

        if ($websitePageUuid) {
            $websitePage = $website->websitePages()->where(['uuid' => $websitePageUuid])->firstOrFail();
        } else {
            $websitePage = $website->websitePages()->wherePivot('is_homepage', 1)->firstOrFail();
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

        return $website->websitePages()
            ->whereIn('type', [WebsitePage::ARTICLE_CATEGORY_TYPE, WebsitePage::HOME_ARTICLES_TYPE, WebsitePage::ARTICLE_DETAIL_TYPE])
            ->get();
    }

    public function getProductWebsitePagesByDomain($domainName)
    {
        $website = (new Website())->whereHas('domain', function ($query) use ($domainName) {
            $query->where([
                ['name', $domainName],
                ['verified_at', '!=', null]
            ]);
        })
            ->where('publish_status', Website::PUBLISHED_PUBLISH_STATUS)
            ->firstOrFail();
        return $website->websitePages()
            ->whereIn('type', [WebsitePage::PRODUCT_CATEGORY_TYPE, WebsitePage::PRODUCT_DETAIL_TYPE, WebsitePage::HOME_PRODUCTS_TYPE])
            ->get();
    }

    public function getNewsWebsitePagesByWebsite($websiteUuid)
    {
        $website = (new Website())->where('uuid', $websiteUuid)->firstOrFail();

        return $website->websitePages()
            ->whereIn('type', [WebsitePage::ARTICLE_CATEGORY_TYPE, WebsitePage::HOME_ARTICLES_TYPE, WebsitePage::ARTICLE_DETAIL_TYPE])
            ->get();
    }

    public function getProductWebsitePagesByWebsite($websiteUuid)
    {
        $website = (new Website())->where('uuid', $websiteUuid)->firstOrFail();

        return $website->websitePages()
            ->whereIn('type', [WebsitePage::PRODUCT_CATEGORY_TYPE, WebsitePage::PRODUCT_DETAIL_TYPE, WebsitePage::HOME_PRODUCTS_TYPE])
            ->get();
    }

    public function renderContent($websitePage, $article)
    {
        $replaceArticleService = new ReplaceArticleService();
        $replaceCategoryService = new ReplaceCategoryService();
        $category = $article->articleCategory;
        $websitePage->html_template = $replaceArticleService->replaceListArticleSpecific($websitePage->html_template, $websitePage);

        $searchReplaceMap = $replaceArticleService->searchReplaceMapForArticle($article);
        $websitePage->html_template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->html_template);
        $websitePage->html_template = $replaceCategoryService->replaceCategoryInArticle($websitePage->html_template, $category);

        return $websitePage;
    }

    public function renderContentForArticleCategory($websitePage, $articleCategory)
    {
        $replaceChildrenCategoryService = new ReplaceChildrenCategoryService();
        $replaceCategoryService = new ReplaceCategoryService();
        $websitePage->html_template = $replaceChildrenCategoryService->replaceChildrenCategory($websitePage->html_template, $articleCategory);

        $searchArticleReplaceMap = $replaceCategoryService->searchReplaceMapForCategory($articleCategory);
        $websitePage->html_template = str_replace(array_keys($searchArticleReplaceMap), $searchArticleReplaceMap, $websitePage->html_template);

        $replaceArticleService = new ReplaceArticleService();
        $websitePage->html_template = $replaceArticleService->replaceListArticleSpecific($websitePage->html_template, $websitePage);
        $websitePage->html_template = $replaceArticleService->replaceListArticle($websitePage->html_template, $articleCategory, $websitePage);

        return $websitePage;
    }

    public function renderContentForHomeArticles($websitePage)
    {
        $replaceArticleService = new ReplaceArticleService();
        $replaceCategoryService = new ReplaceCategoryService();
        $websitePage->html_template = $replaceCategoryService->replaceListCategory($websitePage->html_template, $websitePage);
        $websitePage->html_template = $replaceArticleService->replaceListArticleForPageHome($websitePage->html_template, $websitePage);

        return $websitePage;
    }


    public function renderContentForNewsHeader($websitePage)
    {
        $replaceCategoryService = new ReplaceCategoryService();
        $websitePage->html_template = $replaceCategoryService->replaceListCategoryMenu($websitePage->html_template);

        return $websitePage;
    }

    public function renderContentForProductDetail($websitePage, $productDetailData)
    {
        $replaceProductService = new ReplaceProductService();
        $replaceProductCategoryService = new ReplaceProductCategoryService();
        $replaceBrandService = new ReplaceBrandService();
        $product = $productDetailData;
        $category = $productDetailData['categories'][0];
        $brand = $productDetailData['brand'];
        if (!empty($brand)) {
            $websitePage->html_template = $replaceBrandService->replaceBrand($websitePage->html_template, $brand);
        }
        $searchReplaceMap = $replaceProductService->searchReplaceMapForProduct($product);

        $websitePage->html_template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->html_template);
        $websitePage->html_template = $replaceProductService->replaceListProductSpecific($websitePage->html_template, $websitePage);
        $websitePage->html_template = $replaceProductCategoryService->replaceCategoryInProduct($websitePage->html_template, $category);

        return $websitePage;
    }

    public function renderContentForProductCategory($websitePage, $productCategoryData)
    {
        $replaceChildrenProductCategoryService = new ReplaceChildrenProductCategoryService();
        $replaceProductCategoryService = new ReplaceProductCategoryService();
        $category = $productCategoryData['category'];

        $websitePage->html_template = $replaceChildrenProductCategoryService->replaceChildrenProductCategory($websitePage->html_template, $category);

        $searchProductCategoryReplaceMap = $replaceProductCategoryService->searchReplaceMapForCategory($category);
        $websitePage->html_template = str_replace(array_keys($searchProductCategoryReplaceMap), $searchProductCategoryReplaceMap, $websitePage->html_template);
        $replaceProductService = new ReplaceProductService();
        $websitePage->html_template = $replaceProductService->replaceListProductSpecific($websitePage->html_template, $websitePage);
        $websitePage->html_template = $replaceProductService->replaceListProduct($websitePage->html_template, $category, $websitePage);

        return $websitePage;
    }

    public function renderContentForHomeProducts($websitePage)
    {
        $replaceProductService = new ReplaceProductService();
        $replaceProductCategoryService = new ReplaceProductCategoryService();
        $websitePage->html_template = $replaceProductCategoryService->replaceListProductCategory($websitePage->html_template);
        $websitePage->html_template = $replaceProductService->replaceListProductForPageHome($websitePage->html_template, $websitePage);

        return $websitePage;
    }

    public function renderContentForProductHeader($websitePage)
    {
        $replaceProductCategoryService = new ReplaceProductCategoryService();
        $websitePage->template = $replaceProductCategoryService->replaceListProductCategoryMenu($websitePage->template);

        return $websitePage;
    }
}
