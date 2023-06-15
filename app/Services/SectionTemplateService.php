<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\SectionTemplateQueryBuilder;
use App\Models\SectionTemplate;

class SectionTemplateService extends AbstractService
{
    protected $modelClass = SectionTemplate::class;

    protected $modelQueryBuilderClass = SectionTemplateQueryBuilder::class;

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findSectionTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }

    public function showSectionTemplateForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [SectionTemplate::PENDING_PUBLISH_STATUS, SectionTemplate::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function showSectionTemplateDefaultById($id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', SectionTemplate::PUBLISHED_PUBLISH_STATUS],
            ['is_default', true],
            ['uuid', $id]
        ]);
    }
}
