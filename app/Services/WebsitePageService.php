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

    public function renderContent($websitePage, $article = null, $articleCategory = null)
    {
        if ($websitePage->type == WebsitePage::ARTICLE_DETAIL_TYPE) {
            $searchReplaceMap = [
                '{article.title}' => $article->title ?? null,
                '{article.content}' => $article->content ?? null,
                '{article.video}' => $articleCategory->video ?? null,
                '{article.image}' => $articleCategory->image ?? null,
                '{article.keyword}' => $article->keyword ?? null,
                '{article.description}' => $article->description ?? null,
                '{article.short_content}' => $article->short_content ?? null,
            ];
        } else {
            if (preg_match('/{categorylist}(.*?){\/categorylist}/s', $websitePage->template, $matches)) {
                $contentInsideCategoryList = $matches[1];
                $categoryList = '';
                foreach ($articleCategory->articles as $article) {
                    $searchReplaceMap = [
                        '{article.title}' => $article->title ?? null,
                        '{article.content}' => $article->content ?? null,
                        '{article.video}' => $articleCategory->video ?? null,
                        '{article.image}' => $articleCategory->image ?? null,
                        '{article.keyword}' => $article->keyword ?? null,
                        '{article.description}' => $article->description ?? null,
                        '{article.short_content}' => $article->short_content ?? null,
                    ];
                    $categoryList .= Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $contentInsideCategoryList);
                }
                $websitePage->template = preg_replace('/{categorylist}(.*?){\/categorylist}/s', $categoryList, $websitePage->template);
            }
            $searchReplaceMap = [
                '{category.title}' => $articleCategory->title ?? null,
                '{category.content}' => $articleCategory->content ?? null,
                '{category.feature_image}' => $articleCategory->feature_image ?? null,
                '{category.image}' => $articleCategory->image ?? null,
                '{category.keyword}' => $articleCategory->keyword ?? null,
                '{category.description}' => $articleCategory->description ?? null,
                '{category.short_content}' => $articleCategory->short_content ?? null,
            ];
        }
        $websitePage->template = Str::replace(array_keys($searchReplaceMap), $searchReplaceMap, $websitePage->template);

        return $websitePage;
    }
}
