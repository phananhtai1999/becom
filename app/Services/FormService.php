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

    public function showFormDefaultById($id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', Form::PUBLISHED_PUBLISH_STATUS],
            ['contact_list_uuid', null],
            ['uuid', $id]
        ]);
    }
}
