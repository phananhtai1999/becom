<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Form;
use App\Models\QueryBuilders\FormQueryBuilder;

class FormService extends AbstractService
{
    protected $modelClass = Form::class;

    protected $modelQueryBuilderClass = FormQueryBuilder::class;

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFormDefaultWithPagination($publishedStatus, $perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return FormQueryBuilder::searchQuery($search, $searchBy)
            ->where('publish_status', $publishedStatus)
            ->whereNull('contact_list_uuid')
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function indexFormByPublishStatus($publishStatus, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        return FormQueryBuilder::searchQuery($search, $searchBy)->where('publish_status', $publishStatus)
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findFormByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }
}
