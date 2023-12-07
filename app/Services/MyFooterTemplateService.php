<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\FooterTemplate;
use App\Models\QueryBuilders\FooterTemplateQueryBuilder;
use App\Models\QueryBuilders\MyFooterTemplateQueryBuilder;

class MyFooterTemplateService extends AbstractService
{
    protected $modelClass = FooterTemplate::class;

    protected $modelQueryBuilderClass = MyFooterTemplateQueryBuilder::class;

    /**
     * @param $perPage
     * @param $page
     * @param $columns
     * @param $pageName
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function getMyFooterTemplatesWithTopActive($perPage, $page, $columns, $pageName, $search, $searchBy)
    {
        return MyFooterTemplateQueryBuilder::searchQuery($search, $searchBy)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, $columns, $pageName, $page);
    }

    public function showMyFooterTemplate($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()],
            ['uuid', $id],
        ]);
    }

}
