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

    public function showMyFooterTemplate($id)
    {
        return $this->findOneWhereOrFail([
           ['user_uuid', auth()->user()->getKey()],
           ['uuid',$id],
        ]);
    }

}
