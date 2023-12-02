<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ArticleCategory;
use App\Models\QueryBuilders\ArticleCategoryQueryBuilder;

class ArticleCategoryService extends AbstractService
{
    protected $modelClass = ArticleCategory::class;

    protected $modelQueryBuilderClass = ArticleCategoryQueryBuilder::class;

    /**
     * @param $parentId
     * @return array
     */
    function getArticleCategoriesPublic($parentId = null) {
//        Cách 1
        $all_categories = [];
        $categories = $this->model->where('publish_status', ArticleCategory::PUBLISHED_PUBLISH_STATUS)->where('parent_uuid', $parentId)
            ->select('uuid', 'image','title', 'slug',
                'user_uuid', 'publish_status', 'parent_uuid',
                'created_at', 'updated_at', 'deleted_at')->get();
        foreach ($categories as $cate) {
            $cate['children'] = $this->getArticleCategoriesPublic($cate->uuid);
            $all_categories[] = $cate;
        }

        return $all_categories;

        //Cách 2
//        $allCategory = [];
//        $categories = $this->model->where('publish_status' , 1)->where('parent_uuid', $parentId)->get();
//        foreach ($categories as $cate) {
//            $allCategory[] = $cate;
//            $allCategory = array_merge($allCategory, $this->getArticleCategoriesPublic($cate->uuid));
//        }
//
//        return $allCategory;

    }

    /**
     * @param $id
     * @return mixed
     */
    public function showArticleCategoryPublic($id)
    {
        $model = $this->findOneWhereOrFail([
            'publish_status' => ArticleCategory::PUBLISHED_PUBLISH_STATUS,
            'uuid' => $id
        ]);

        if ($model->ancestors()->where('publish_status', ArticleCategory::PENDING_PUBLISH_STATUS)->count()){
            return false;
        }
        return $model;
    }

    /**
     * @param $parentId
     * @return array|mixed
     */
    public function getListArticleCategoriesPublic($parentId = null)
    {
        $allCategory = [];
        $categories = $this->model->where('publish_status' , 1)->where('parent_uuid', $parentId)->get();
        foreach ($categories as $cate) {
            $allCategory[] = $cate;
            $allCategory = array_merge($allCategory, $this->getListArticleCategoriesPublic($cate->uuid));
        }

        return $allCategory;
    }

    /**
     * @return array
     */
    public function getListArticleCategoryUuidsPublic()
    {
        $articleCategoryPublic = $this->getListArticleCategoriesPublic();
        return (collect($articleCategoryPublic)->pluck('uuid')->toArray());
    }

    public function getArticleCategoriesPublicWithPagination($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return ArticleCategoryQueryBuilder::searchQuery($search, $searchBy)
            ->whereIn('uuid', $this->getListArticleCategoryUuidsPublic())
            ->paginate($perPage, $columns, $pageName, $page);
    }

    public function getArticleCategoriesByRootUuid($uuid)
    {
        return $this->findOrFailById($uuid)->getDescendantsAndSelf();
    }

    public function getLastArticleCategory()
    {
        return $this->model->orderBy('created_at', 'desc')->first();
    }

}
