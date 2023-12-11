<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Article;
use App\Models\QueryBuilders\ArticleQueryBuilder;
use App\Models\Role;
use Carbon\Carbon;

class ArticleService extends AbstractService
{
    protected $modelClass = Article::class;

    protected $modelQueryBuilderClass = ArticleQueryBuilder::class;

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getArticlePublicWithPagination($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        $articleCategoryPublicByUuids = (new ArticleCategoryService())->getListArticleCategoryUuidsPublic();

        return ArticleQueryBuilder::searchQuery($search, $searchBy)
            ->where([
                ['publish_status', Article::PUBLISHED_PUBLISH_STATUS],
                ['content_for_user', Article::PUBLIC_CONTENT_FOR_USER]
            ])
            ->where(function ($query) use ($articleCategoryPublicByUuids) {
                $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                    ->orWhereNull('article_category_uuid');
            })
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function loadAllArticles($perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        return ArticleQueryBuilder::searchQuery($search, $searchBy)->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @return bool
     */
    public function paidUser()
    {
        //Check user has paid 1 of 3 packages
        $creditPackageHistory = (new CreditPackageHistoryService())->getCreditPackageHistoryOfCurrentUser();
        $addOnSubscriptionHistory = (new AddOnSubscriptionHistoryService())->getAddOnSubscriptionHistoryOfCurrentUser();
        $subscriptionHistory = (new SubscriptionHistoryService())->getSubscriptionHistoryOfCurrentUser();

        return ($creditPackageHistory || $addOnSubscriptionHistory || $subscriptionHistory);
    }

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @param $arrayListContentForUser
     * @param $publishStatus
     * @return mixed
     */
    public function getArticleContentPublic($perPage, $columns, $pageName, $page, $search, $searchBy, $arrayListContentForUser, $publishStatus)
    {
        //Get  Article Category Public
        $articleCategoryPublicByUuids = (new ArticleCategoryService())->getListArticleCategoryUuidsPublic();
        $config = (new ConfigService())->findConfigByKey('time_allowed_view_articles_of_editor');
        if ($arrayListContentForUser === Article::EDITOR_CONTENT_FOR_USER && $config) {
            return ArticleQueryBuilder::searchQuery($search, $searchBy)
                ->whereIn('publish_status', $publishStatus)
                ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
                ->where(function ($query) use ($articleCategoryPublicByUuids) {
                    $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                        ->orWhereNull('article_category_uuid');
                })
                ->where('updated_at', '>=', Carbon::now()->subDays($config->value))
                ->paginate($perPage, $columns, $pageName, $page);
        } else {
            return ArticleQueryBuilder::searchQuery($search, $searchBy)
                ->whereIn('publish_status', $publishStatus)
                ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
                ->where(function ($query) use ($articleCategoryPublicByUuids) {
                    $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                        ->orWhereNull('article_category_uuid');
                })
                ->paginate($perPage, $columns, $pageName, $page);
        }
    }

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @param $arrayListContentForUser
     * @param $publishStatus
     * @return mixed
     */
    public function getArticleManager($perPage, $columns, $pageName, $page, $search, $searchBy, $arrayListContentForUser, $publishStatus)
    {
        //Get  Article Category Public
        $articleCategoryPublicByUuids = (new ArticleCategoryService())->getListArticleCategoryUuidsPublic();
        $config = (new ConfigService())->findConfigByKey('time_allowed_view_articles_of_editor');
        if ($arrayListContentForUser === Article::EDITOR_CONTENT_FOR_USER && $config) {
            return ArticleQueryBuilder::searchQuery($search, $searchBy)
                ->whereIn('publish_status', $publishStatus)
                ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
                ->where(function ($query) use ($articleCategoryPublicByUuids) {
                    $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                        ->orWhereNull('article_category_uuid');
                })
                ->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                ->where('updated_at', '>=', Carbon::now()->subDays($config->value))
                ->paginate($perPage, $columns, $pageName, $page);
        } else {
            return ArticleQueryBuilder::searchQuery($search, $searchBy)
                ->whereIn('publish_status', $publishStatus)
                ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
                ->where(function ($query) use ($articleCategoryPublicByUuids) {
                    $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                        ->orWhereNull('article_category_uuid');
                })
                ->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                ->paginate($perPage, $columns, $pageName, $page);
        }
    }

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getArticleContentPublicWithPagination($perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        //Check guest
        if (auth()->guest()) {
            return $this->getArticleContentPublic($perPage, $columns, $pageName, $page, $search, $searchBy, Article::PUBLIC_CONTENT_FOR_USER, Article::PUBLISHED_PUBLISH_STATUS);
        }
        //Check auth:api
        //get Article Content Public
        $publishStatus = [Article::PUBLISHED_PUBLISH_STATUS];
        if ($this->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            //Admin
            $contentForUSer = Article::ADMIN_CONTENT_FOR_USER;
        } elseif ($this->paidUser()) {
            //Payment
            $contentForUSer = Article::PAYMENT_CONTENT_FOR_USER;
        } elseif ($this->checkUserRoles([Role::ROLE_EDITOR])) {
            //Editor
            $contentForUSer = Article::EDITOR_CONTENT_FOR_USER;
        } else {
            //Login
            $contentForUSer = Article::LOGIN_CONTENT_FOR_USER;
        }

        return $this->getArticleContentPublic($perPage, $columns, $pageName, $page, $search, $searchBy, $contentForUSer, $publishStatus);
    }

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getArticleManagerWithPagination($perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        //Check guest
        if (auth()->guest()) {
            return $this->getArticleManager($perPage, $columns, $pageName, $page, $search, $searchBy, Article::PUBLIC_CONTENT_FOR_USER, Article::PUBLISHED_PUBLISH_STATUS);
        }

        //Check auth:api
        //get Article By Permission
        $publishStatus = [Article::PUBLISHED_PUBLISH_STATUS];
        if ($this->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            //Admin
            return $this->loadAllArticles($perPage, $columns, $pageName, $page, $search, $searchBy);
        } elseif ($this->paidUser()) {
            //Check current user role
            if ($this->checkUserRoles([Role::ROLE_EDITOR])) {
                $publishStatus = [Article::PENDING_PUBLISH_STATUS, Article::PUBLISHED_PUBLISH_STATUS, Article::BLOCKED_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS];
            }
            //Payment
            $contentForUSer = Article::PAYMENT_CONTENT_FOR_USER;
        } elseif ($this->checkUserRoles([Role::ROLE_EDITOR])) {
            //Editor
            $publishStatus = [Article::PENDING_PUBLISH_STATUS, Article::PUBLISHED_PUBLISH_STATUS, Article::BLOCKED_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS];
            $contentForUSer = Article::EDITOR_CONTENT_FOR_USER;
        } else {
            //Login
            $contentForUSer = Article::LOGIN_CONTENT_FOR_USER;
        }

        return $this->getArticleManager($perPage, $columns, $pageName, $page, $search, $searchBy, $contentForUSer, $publishStatus);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showArticlePublic($id)
    {
        $articleCategoryPublicByUuids = (new ArticleCategoryService())->getListArticleCategoryUuidsPublic();
        return $this->model->where([
            ['uuid', $id],
            ['publish_status', Article::PUBLISHED_PUBLISH_STATUS],
            ['content_for_user', Article::PUBLIC_CONTENT_FOR_USER]
        ])->where(function ($query) use ($articleCategoryPublicByUuids) {
            $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                ->orWhereNull('article_category_uuid');
        })->firstOrFail();
    }

    public function moveArticlesCategoryOfArticles($articles, $goCategoryUuid)
    {
        foreach ($articles as $article) {
            $this->update($article, [
                'article_category_uuid' => $goCategoryUuid
            ]);
        }
    }

    public function showArticleForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [Article::PENDING_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS, Article::DRAFT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function totalEditorArticleChart($startDate, $endDate)
    {
        return $this->model->selectRaw("COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 4, 1, NULL ) ) as reject")
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->first()->setAppends([])->toArray();
    }

    public function getArticlesChartByDateFormat($dateFormat, $startDate, $endDate)
    {
        return $this->model->selectRaw("date_format(updated_at, '{$dateFormat}') as label,
        COUNT(IF( publish_status = 1, 1, NULL ) ) as approve,
        COUNT(IF( publish_status = 3, 1, NULL ) ) as pending,
        COUNT(IF( publish_status = 4, 1, NULL ) ) as reject")
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

    public function editorArticleChart($groupBy, $startDate, $endDate)
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

        $charts = $this->getArticlesChartByDateFormat($dateFormat, $startDate, $endDate)->keyBy('label');
        foreach ($times as $time) {
            $mailByTime = $charts->first(function ($item, $key) use ($time) {
                return $key == $time;
            });

            if ($mailByTime) {
                $result[] = [
                    'label' => $time,
                    'approve' => $mailByTime->approve,
                    'pending' => $mailByTime->pending,
                    'reject' => $mailByTime->reject
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

    /**
     * @param $contentType
     * @param $content
     * @param $contentTranslate
     * @return array
     */
    public function formatContent($contentType, $content, $contentTranslate)
    {
        $formatContent = [];
        if ($contentType == Article::PARAGRAPH_CONTENT_TYPE) {
            $formatContent['content'] = json_decode($content) ?? $content;
            //Check contents by login, user role
            if (is_array($contentTranslate)) {
                //Login and user role is admin, editor, root
                $formatContent['contents'] = array_map(function ($value) {
                    $arrayContent = json_decode($value, true);
                    return $arrayContent ?? $value;
                }, $contentTranslate);
            } else {
                $formatContent['contents'] = json_decode($content) ?? $content;
            }
        } else {
            $formatContent['content'] = $content;
            $formatContent['contents'] = $contentTranslate;
        }

        return $formatContent;
    }

    /**
     * @param $content
     * @param $contentType
     * @return array|false[]|string[]
     */
    public function mapTypeLabelToContent($content, $contentType)
    {
        if ($contentType == Article::PARAGRAPH_CONTENT_TYPE) {
            $array = [];
            foreach ($content as $key => $jsonString) {
                $originalArray = json_decode($jsonString, true);

                $typeUuids = array_column($originalArray, 'type_uuid');

                $articles = (new ParagraphTypeService())->pluckField($typeUuids);

                $array[$key] = array_map(function ($item) use ($articles) {
                    $typeLabel = $articles[$item['type_uuid']] ?? null;
                    return array_merge($item, ['type_label' => $typeLabel]);
                }, $originalArray);
            }

            return array_map(function ($item) {
                return json_encode($item);
            }, $array);
        }

        return $content;
    }

    public function getLastArticle()
    {
        return $this->model->orderBy('created_at', 'desc')->first();
    }
}
