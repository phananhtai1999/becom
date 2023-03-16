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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getArticlePublicWithPagination($perPage, $page, $columns, $pageName )
    {
        return ArticleQueryBuilder::initialQuery()
            ->where('publish_status', Article::PUBLISHED_PUBLISH_STATUS)
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function showArticlePublic($id)
    {
        return $this->findOneWhereOrFail([
           'uuid' => $id,
           'publish_status' => Article::PUBLISHED_PUBLISH_STATUS
        ]);
    }
}
