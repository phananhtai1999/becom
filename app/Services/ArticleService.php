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
     * @return mixed
     */
    public function getArticleByPermission($perPage, $columns, $pageName, $page, $search, $searchBy, $arrayListContentForUser)
    {
        //Get  Article Category Public
        $articleCategoryPublicByUuids = (new ArticleCategoryService())->getListArticleCategoryUuidsPublic();

        return ArticleQueryBuilder::searchQuery($search, $searchBy)
            ->where('publish_status', Article::PUBLISHED_PUBLISH_STATUS)
            ->where(function ($query) use ($articleCategoryPublicByUuids) {
                $query->whereIn('article_category_uuid', $articleCategoryPublicByUuids)
                    ->orWhereNull('article_category_uuid');
            })
            ->whereIn('content_for_user', config('articlepermission.' . $arrayListContentForUser))
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
    public function getArticleByPermissionWithPagination($perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        //Check guest
        if (auth()->guest()) {
            return $this->getArticleByPermission($perPage, $columns, $pageName, $page, $search, $searchBy, Article::PUBLIC_CONTENT_FOR_USER);
        }

        //Check auth:api
        //get Article By Permission
        if (auth()->user()->roles->whereIn('slug', ["admin"])->count()) {
            //Admin
            return $this->loadAllArticles($perPage, $columns, $pageName, $page, $search, $searchBy);
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
        return $this->getArticleByPermission($perPage, $columns, $pageName, $page, $search, $searchBy, $contentForUSer);
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
}
