<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Article;
use App\Models\QueryBuilders\ArticleQueryBuilder;

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

        return ArticleQueryBuilder::searchQuery($search, $searchBy)
            ->whereIn('publish_status', $publishStatus)
            ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
            ->where(function ($query) use ($articleCategoryPublicByUuids) {
                $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                    ->orWhereNull('article_category_uuid');
            })
            ->paginate($perPage, $columns, $pageName, $page);
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

        return ArticleQueryBuilder::searchQuery($search, $searchBy)
            ->whereIn('publish_status', $publishStatus)
            ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
            ->where(function ($query) use ($articleCategoryPublicByUuids) {
                $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                    ->orWhereNull('article_category_uuid');
            })
            ->where('user_uuid', auth()->user()->getkey())
            ->paginate($perPage, $columns, $pageName, $page);
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
        if (auth()->user()->roles->whereIn('slug', ["admin","root"])->count()) {
            //Admin
            $contentForUSer = Article::ADMIN_CONTENT_FOR_USER;
        } elseif ($this->paidUser()) {
            //Payment
            $contentForUSer = Article::PAYMENT_CONTENT_FOR_USER;
        } elseif (auth()->user()->roles->whereIn('slug', ["editor"])->count()) {
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
        if (auth()->user()->roles->whereIn('slug', ["admin","root"])->count()) {
            //Admin
            return $this->loadAllArticles($perPage, $columns, $pageName, $page, $search, $searchBy);
        } elseif ($this->paidUser()) {
            //Check current user role
            if (auth()->user()->roles->whereIn('slug', ["editor"])->count()) {
                $publishStatus = [Article::PENDING_PUBLISH_STATUS, Article::PUBLISHED_PUBLISH_STATUS, Article::BLOCKED_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS];
            }
            //Payment
            $contentForUSer = Article::PAYMENT_CONTENT_FOR_USER;
        } elseif (auth()->user()->roles->whereIn('slug', ["editor"])->count()) {
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
        foreach ($articles as $article){
            $this->update($article, [
                'article_category_uuid' => $goCategoryUuid
            ]);
        }
    }

    public function showArticleForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [Article::PENDING_PUBLISH_STATUS, Article::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }
}
