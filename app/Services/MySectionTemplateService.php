<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MySectionTemplateQueryBuilder;
use App\Models\SectionTemplate;

class MySectionTemplateService extends AbstractService
{
    protected $modelClass = SectionTemplate::class;

    protected $modelQueryBuilderClass = MySectionTemplateQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMySectionTemplateByUuid($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMySectionTemplateByUuid($id)
    {
        $SectionTemplate = $this->showMySectionTemplateByUuid($id);

        return $this->destroy($SectionTemplate->getKey());
    }

    public function getCanUseUuidsSectionTemplates()
    {
        return $this->model->doesntHave('headerWebsite')->doesntHave('footerWebsite')
            ->where([
                ['user_uuid', auth()->user()],
                ['app_id', auth()->appId()]
            ])->get()->pluck('uuid');
    }

    public function getIsCanUseSectionTemplates($request)
    {
        $isCanUseSectionTemplates = $this->getCanUseUuidsSectionTemplates();
        $indexRequest = $this->getIndexRequest($request);

        return $this->modelQueryBuilderClass::searchQuery($indexRequest['search'], $indexRequest['search_by'])
            ->whereIn('uuid', $isCanUseSectionTemplates)
            ->paginate($indexRequest['per_page'], $indexRequest['columns'], $indexRequest['page_name'], $indexRequest['page']);
    }
}
