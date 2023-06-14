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

    public function showFormForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [Form::PENDING_PUBLISH_STATUS, Form::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
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
