<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;
use Illuminate\Support\Facades\DB;

class MailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    protected $modelQueryBuilderClass = MailTemplateQueryBuilder::class;

    /**
     * @param $id
     * @return bool
     */
    public function checkExistsMailTemplateInTables($id)
    {
        $mailTemplate = $this->findOrFailById($id);

        $campaigns = $mailTemplate->campaigns->toArray();

        if (!empty($campaigns)) {
            return true;
        }

        return false;
    }

    /**
     * @param $publishStatus
     * @param $id
     * @return mixed
     */
    public function findMailTemplateByKeyAndPublishStatus($publishStatus, $id)
    {
        return $this->findOneWhereOrFail([
            ['publish_status', $publishStatus],
            ['uuid', $id]
        ]);
    }

    public function showMailTemplateForEditorById($id)
    {
        return $this->model->whereIn('publish_status', [MailTemplate::PENDING_PUBLISH_STATUS, MailTemplate::REJECT_PUBLISH_STATUS])
            ->where('uuid', $id)->firstOrFail();
    }

    public function moveBusinessCategoryOfMailTemplates($mailTemplates, $goBusinessCategoryUuid)
    {
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
               'business_category_uuid' => $goBusinessCategoryUuid
            ]);
        }
    }

    public function movePurposeOfMailTemplates($mailTemplates, $goPurposeUuid)
    {
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
                'purpose_uuid' => $goPurposeUuid
            ]);
        }
    }
}
