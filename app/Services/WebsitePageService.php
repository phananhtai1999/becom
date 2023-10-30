<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Article;
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

    public function renderContent($websitePage, $article)
    {
        $searchReplaceMap = [
            '{article.title}' => $article->title ?? null,
            '{article.content}' => $article->content ?? null,
            '{article.video}' => $article->video ?? null,
            '{article.image}' => $article->image ?? null,
            '{article.keyword}' => $article->keyword ?? null,
            '{article.description}' => $article->description ?? null,
            '{article.short_content}' => $article->short_content ?? null,
        ];
        $websitePage->template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->template);

        return $websitePage;
    }

    public function renderContentForArticleCategory($websitePage, $articleCategory)
    {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match($pattern, $websitePage->template, $articleCount);
        $articleCount = isset($articleCount[1]) ? (int)$articleCount[1] : 10;
        $articlesData = Article::where('article_category_uuid', $articleCategory->uuid)->orderBy('created_at', 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        $websitePage->template = preg_replace_callback($pattern, function ($matches) use ($articlesData) {
            $articleData = $articlesData->shift();
            if (!$articleData) {
                return $matches[0];
            }

            $searchReplaceMap = [
                '{article.title}' => $articleData->title ?? null,
                '{article.content}' => $articleData->content ?? null,
                '{article.video}' => $articleData->video ?? null,
                '{article.image}' => $articleData->image ?? null,
                '{article.keyword}' => $articleData->keyword ?? null,
                '{article.description}' => $articleData->description ?? null,
                '{article.short_content}' => $articleData->short_content ?? null,
            ];

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $websitePage->template);

        $searchReplaceMap = [
            '{category.title}' => $articleCategory->title ?? null,
            '{category.content}' => $articleCategory->content ?? null,
            '{category.feature_image}' => $articleCategory->feature_image ?? null,
            '{category.image}' => $articleCategory->image ?? null,
            '{category.keyword}' => $articleCategory->keyword ?? null,
            '{category.description}' => $articleCategory->description ?? null,
            '{category.short_content}' => $articleCategory->short_content ?? null,
        ];
        $websitePage->template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->template);

        return $websitePage;
    }

    public function renderContentForHomeArticles($websitePage)
    {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match($pattern, $websitePage->template, $articleCount);
        $articleCount = isset($articleCount[1]) ? (int)$articleCount[1] : 10;

        $articles_data = Article::orderBy('created_at', 'DESC')->paginate($articleCount);
        $pattern = '/<article.*?>(.*?)<\/article>/s';
        $websitePage->template = preg_replace_callback($pattern, function ($matches) use ($articles_data) {
            $article_data = $articles_data->shift();
            if (!$article_data) {
                return $matches[0];
            }

            $searchReplaceMap = [
                '{home_article.title}' => $article_data->title ?? null,
                '{home_article.content}' => $article_data->content ?? null,
                '{home_article.video}' => $article_data->video ?? null,
                '{home_article.image}' => $article_data->image ?? null,
                '{home_article.keyword}' => $article_data->keyword ?? null,
                '{home_article.description}' => $article_data->description ?? null,
                '{home_article.short_content}' => $article_data->short_content ?? null,
            ];

            return str_replace(array_keys($searchReplaceMap), $searchReplaceMap, $matches[0]);
        }, $websitePage->template);

        return $websitePage;
    }

    private function getDataCount($websitePage) {
        $pattern = '/data-article-count="(\d+)"/';
        preg_match($pattern, $websitePage->template, $articleCount);
        $articleCount = isset($articleCount[1]) ? (int)$articleCount[1] : 10;
    }
}
