<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Article;
use App\Models\ArticleCategory;
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
        $searchReplaceMap = $this->searchReplaceMapForArticle($article);
        $websitePage->template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->template);

        return $websitePage;
    }

    public function renderContentForArticleCategory($websitePage, $articleCategory)
    {
        preg_match('/data-children-category-count="(\d+)"/', $websitePage->template, $childrenCategoryCount);
        $childrenCategoryCount = isset($childrenCategoryCount[1]) ? (int)$childrenCategoryCount[1] : 10;
        preg_match('/children-category-sort="(.*?)"/', $websitePage->template, $sortName);
        preg_match('/children-category-sort-order="(.*?)"/', $websitePage->template, $sortOrder);
        $childrenCategoriesData = ArticleCategory::where('parent_uuid', $articleCategory->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($childrenCategoryCount);
        $websitePage->template = preg_replace_callback('/<children_category.*?>(.*?)<\/children_category>/s', function ($matches) use ($childrenCategoriesData) {
            $childrenCategoryData = $childrenCategoriesData->shift();

            if (!$childrenCategoryData) {
                return $matches[0];
            }

            $childrenCategoriesUuid = ArticleCategory::where('parent_uuid', $childrenCategoryData->uuid)->get()->pluck('uuid');
            $article = Article::whereIn('article_category_uuid', array_merge($childrenCategoriesUuid->toArray(), [$childrenCategoryData->uuid]))->orderBy('created_at', 'DESC')->first();
            if ($article) {
                $searchReplaceArticleMap = $this->searchReplaceMapForArticle($article);
                $matches[0] = Str::replace(array_keys($searchReplaceArticleMap), $searchReplaceArticleMap, $matches[0]);
            }

            $searchReplaceMap = $this->searchReplaceMapForChildrenCategory($childrenCategoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
            $matches[0] = $this->replaceGrandChildrenCategory($matches[0], $childrenCategoryData);

            return $matches[0];

        }, $websitePage->template);

        $searchArticleReplaceMap = $this->searchReplaceMapForCategory($articleCategory);
        $websitePage->template = str_replace(array_keys($searchArticleReplaceMap), $searchArticleReplaceMap, $websitePage->template);

        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $websitePage->template, $articleCount);
        $articleCount = isset($articleCount[1]) ? array_sum($articleCount[1]) : 10;
        preg_match('/article-sort="(.*?)"/', $websitePage->template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $websitePage->template, $sortOrder);
        $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        $websitePage->template = preg_replace_callback($pattern, function ($matches) use ($articlesData) {
            $articleData = $articlesData->shift();
            if (!$articleData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForArticle($articleData);

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $websitePage->template);

        return $websitePage;
    }

    public function renderContentForHomeArticles($websitePage)
    {

        preg_match('/data-category-count="(\d+)"/', $websitePage->template, $categoryCount);
        $categoryCount = isset($categoryCount[1]) ? (int)$categoryCount[1] : 10;

        //get orderby
        preg_match('/category-sort="(.*?)"/', $websitePage->template, $sortName);
        preg_match('/category-sort-order="(.*?)"/', $websitePage->template, $sortOrder);
        $categoriesData = ArticleCategory::where('parent_uuid', null)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($categoryCount);
        $websitePage->template = preg_replace_callback('/<category.*?>(.*?)<\/category>/s', function ($matches) use ($categoriesData) {
            $categoryData = $categoriesData->shift();

            if (!$categoryData) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForCategory($categoryData);
            $matches[0] = str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
            $matches[0] = $this->replacechildrenCategory($matches[0], $categoryData);

            $childrenCategoriesUuid = ArticleCategory::where('parent_uuid', $categoryData->uuid)->get()->pluck('uuid');
            $article = Article::whereIn('article_category_uuid', array_merge($childrenCategoriesUuid->toArray(), [$categoryData->uuid]))->orderBy('created_at', 'DESC')->first();
            if ($article) {
                $searchReplaceArticleMap = $this->searchReplaceMapForArticle($article);
                $matches[0] = Str::replace(array_keys($searchReplaceArticleMap), $searchReplaceArticleMap, $matches[0]);
            }

            return $matches[0];

        }, $websitePage->template);

        //get number article need to parse
        $pattern = '/data-article-count="(\d+)"/';
        preg_match_all($pattern, $websitePage->template, $matches);
        $numbers = array_map('intval', $matches[1]);
        $articleCount = array_sum($numbers);
        $articleCount = isset($articleCount) ? (int)$articleCount : 10;

        //get orderby
        preg_match('/article-sort="(.*?)"/', $websitePage->template, $sortName);
        preg_match('/article-sort-order="(.*?)"/', $websitePage->template, $sortOrder);
        $articles_data = Article::orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        $websitePage->template = preg_replace_callback($pattern, function ($matches) use ($articles_data) {
            $article_data = $articles_data->shift();
            if (!$article_data) {
                return $matches[0];
            }

            $searchReplaceMap = $this->searchReplaceMapForArticle($article_data);
            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $websitePage->template);

        return $websitePage;
    }

    /**
     * @param $matches
     * @param $childrenCategoryData
     * @return array
     */
    function replaceGrandChildrenCategory($matches, $childrenCategoryData)
    {
        preg_match('/data-grand-children-category-count="(\d+)"/', $matches, $grandChildrenCategoryCount);
        $grandChildrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$grandChildrenCategoryCount[1] : 10;

        //get orderby
        preg_match('/grand-children-category-sort="(.*?)"/', $matches, $sortName);
        preg_match('/grand-children-category-sort-order="(.*?)"/', $matches, $sortOrder);
        $grandChildrenCategoriesData = ArticleCategory::where('parent_uuid', $childrenCategoryData->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($grandChildrenCategoryCount);
        $matches = preg_replace_callback('/<grand_children_category.*?>(.*?)<\/grand_children_category>/s', function ($grandChildMatches) use ($grandChildrenCategoriesData) {
            $grandChildrenCategoryData = $grandChildrenCategoriesData->shift();
            $grandChildSearchReplaceMap = $this->searchReplaceMapForGrandChildrenCategory($grandChildrenCategoryData);

            return str_replace(array_keys($grandChildSearchReplaceMap), $grandChildSearchReplaceMap, $grandChildMatches[0]);
        }, $matches);
        return $matches;
    }

    function replaceChildrenCategory($matches, $categoryData)
    {
        preg_match('/data-children-category-count="(\d+)"/', $matches, $childrenCategoryCount);
        $childrenCategoryCount = isset($grandChildrenCategoryCount[1]) ? (int)$childrenCategoryCount[1] : 10;
        //get orderby
        preg_match('/children-category-sort="(.*?)"/', $matches, $sortName);
        preg_match('/children-category-sort-order="(.*?)"/', $matches, $sortOrder);
        $childrenCategoriesData = ArticleCategory::where('parent_uuid', $categoryData->uuid)->orderBy($sortName[1] ?? 'created_at', $sortOrder[1] ?? 'DESC')->paginate($childrenCategoryCount);
        $matches = preg_replace_callback('/<children_category.*?>(.*?)<\/children_category>/s', function ($childMatches) use ($childrenCategoriesData) {
            $childrenCategoryData = $childrenCategoriesData->shift();

            if (!$childrenCategoryData) {
                return $childMatches[0];
            }
            $childSearchReplaceMap = $this->searchReplaceMapForChildrenCategory($childrenCategoryData);

            return str_replace(array_keys($childSearchReplaceMap), $childSearchReplaceMap, $childMatches[0]);
        }, $matches);
        return $matches;
    }

    private function getDataCount($websitePage)
    {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match($pattern, $websitePage->template, $articleCount);
        $articleCount = isset($articleCount[1]) ? (int)$articleCount[1] : 10;
    }

    private function searchReplaceMapForArticle($article = null)
    {
        if (!empty($article)) {
            return [
                '{article.uuid}' => $article->uuid ?? null,
                '{article.article_category_uuid}' => $article->article_category_uuid ?? null,
                '{article.slug}' => $article->slug ?? null,
                '{article.title}' => $article->title ?? null,
                '{article.content}' => $article->content ?? null,
                '{article.video}' => $article->video ?? null,
                '{article.image}' => $article->image ?? null,
                '{article.keyword}' => $article->keyword ?? null,
                '{article.description}' => $article->description ?? null,
                '{article.short_content}' => $article->short_content ?? null,
            ];
        } else {
            return [
                '{article.uuid}' => 'This is article uuid sample',
                '{article.article_category_uuid}' => 'This is article article_category_uuid sample',
                '{article.slug}' => 'This is article slug sample',
                '{article.title}' => 'This is article title sample',
                '{article.content}' => 'This is article content sample',
                '{article.video}' => 'This is article video sample',
                '{article.image}' => 'This is article image sample',
                '{article.keyword}' => 'This is article keyword sample',
                '{article.description}' => 'This is article description sample',
                '{article.short_content}' => 'This is article short_content sample',
            ];
        }

    }

    private function searchReplaceMapForCategory($articleCategory = null)
    {
        if (!empty($articleCategory)) {
            return [
                '{category.uuid}' => $articleCategory->uuid ?? null,
                '{category.slug}' => $articleCategory->slug ?? null,
                '{category.title}' => $articleCategory->title ?? null,
                '{category.content}' => $articleCategory->content ?? null,
                '{category.feature_image}' => $articleCategory->feature_image ?? null,
                '{category.image}' => $articleCategory->image ?? null,
                '{category.keyword}' => $articleCategory->keyword ?? null,
                '{category.description}' => $articleCategory->description ?? null,
                '{category.short_content}' => $articleCategory->short_content ?? null,
                ];

        } else {
            return [
                '{category.uuid}' => 'This is category uuid sample',
                '{category.slug}' => 'This is category slug sample',
                '{category.title}' => 'This is category title sample',
                '{category.content}' => 'This is category content sample',
                '{category.feature_image}' => 'This is category feature_image sample',
                '{category.image}' => 'This is category image sample',
                '{category.keyword}' => 'This is category keyword sample',
                '{category.description}' => 'This is category description sample',
                '{category.short_content}' => 'This is category short_content sample',
            ];
        }

    }

    private function searchReplaceMapForChildrenCategory($childrenCategory = null)
    {
        if (!empty($childrenCategory)) {
            return [
                '{children_category.uuid}' => $childrenCategory->uuid ?? null,
                '{children_category.slug}' => $childrenCategory->slug ?? null,
                '{children_category.title}' => $childrenCategory->title ?? null,
                '{children_category.content}' => $childrenCategory->content ?? null,
                '{children_category.feature_image}' => $childrenCategory->feature_image ?? null,
                '{children_category.image}' => $childrenCategory->image ?? null,
                '{children_category.keyword}' => $childrenCategory->keyword ?? null,
                '{children_category.description}' => $childrenCategory->description ?? null,
                '{children_category.short_content}' => $childrenCategory->short_content ?? null,
            ];
        } else {

            return [
                '{children_category.uuid}' => 'This is children category sample',
                '{children_category.slug}' => 'This is children category sample',
                '{children_category.title}' => 'This is children category sample',
                '{children_category.content}' => 'This is children category sample',
                '{children_category.feature_image}' => 'This is children category sample',
                '{children_category.image}' => 'This is children category sample',
                '{children_category.keyword}' => 'This is children category sample',
                '{children_category.description}' => 'This is children category sample',
                '{children_category.short_content}' => 'This is children category sample',
            ];
        }
    }

    private function searchReplaceMapForGrandChildrenCategory($grandChildrenCategory = null)
    {
        if (!empty($grandChildrenCategory)) {
            return [
                '{grand_children_category.uuid}' => $grandChildrenCategory->uuid ?? null,
                '{grand_children_category.slug}' => $grandChildrenCategory->slug ?? null,
                '{grand_children_category.title}' => $grandChildrenCategory->title ?? null,
                '{grand_children_category.content}' => $grandChildrenCategory->content ?? null,
                '{grand_children_category.feature_image}' => $grandChildrenCategory->feature_image ?? null,
                '{grand_children_category.image}' => $grandChildrenCategory->image ?? null,
                '{grand_children_category.keyword}' => $grandChildrenCategory->keyword ?? null,
                '{grand_children_category.description}' => $grandChildrenCategory->description ?? null,
                '{grand_children_category.short_content}' => $grandChildrenCategory->short_content ?? null,
            ];
        } else {
            return [
                '{grand_children_category.uuid}' => 'This is grand children category sample',
                '{grand_children_category.slug}' => 'This is grand children category sample',
                '{grand_children_category.title}' => 'This is grand children category sample',
                '{grand_children_category.content}' => 'This is grand children category sample',
                '{grand_children_category.feature_image}' => 'This is grand children category sample',
                '{grand_children_category.image}' => 'This is grand children category sample',
                '{grand_children_category.keyword}' => 'This is grand children category sample',
                '{grand_children_category.description}' => 'This is grand children category sample',
                '{grand_children_category.short_content}' => 'This is grand children category sample',
            ];
        }
    }
}
