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
}
