<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\ArticleCategory;
use App\Models\BusinessCategory;
use App\Models\QueryBuilders\ArticleCategoryQueryBuilder;
use App\Models\QueryBuilders\BusinessCategoryQueryBuilder;

class BusinessCategoryService extends AbstractService
{
    protected $modelClass = BusinessCategory::class;

    protected $modelQueryBuilderClass = BusinessCategoryQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showBusinessCategoryPublic($id)
    {
        $model = $this->findOneWhereOrFail([
            'publish_status' => BusinessCategory::PUBLISHED_PUBLISH_STATUS,
            'uuid' => $id
        ]);

        if ($model->ancestors()->where('publish_status', BusinessCategory::PENDING_PUBLISH_STATUS)->count()){
            return false;
        }
        return $model;
    }

    /**
     * @param $parentId
     * @return array|mixed
     */
    public function getListBusinessCategoriesPublic($parentId = null)
    {
        $allCategory = [];
        $categories = $this->model->where('publish_status' , 1)->where('parent_uuid', $parentId)->get();
        foreach ($categories as $cate) {
            $allCategory[] = $cate;
            $allCategory = array_merge($allCategory, $this->getListBusinessCategoriesPublic($cate->uuid));
        }

        return $allCategory;
    }

    /**
     * @return array
     */
    public function getListBusinessCategoryUuidsPublic()
    {
        $businessCategoryPublic = $this->getListBusinessCategoriesPublic();
        return (collect($businessCategoryPublic)->pluck('uuid')->toArray());
    }

    public function getBusinessCategoriesPublicWithPagination($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return BusinessCategoryQueryBuilder::searchQuery($search, $searchBy)
            ->whereIn('uuid', $this->getListBusinessCategoryUuidsPublic())
            ->paginate($perPage, $columns, $pageName, $page);
    }

}
