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

    public function moveBusinessCategoryOfMailTemplate($businessCategoriesUuids, $goBusinessCategoryUuid)
    {
        $mailTemplates = $this->model->select('uuid','business_category_uuid')->whereIn('business_category_uuid', $businessCategoriesUuids)->get();
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
               'business_category_uuid' => $goBusinessCategoryUuid
            ]);
        }
    }

    public function movePurposeOfMailTemplate($purposeUuid, $goPurposeUuid)
    {
        $mailTemplates = $this->model->select('uuid','purpose_uuid')->where('purpose_uuid', $purposeUuid)->get();
        foreach ($mailTemplates as $mailTemplate){
            $this->update($mailTemplate, [
                'purpose_uuid' => $goPurposeUuid
            ]);
        }
    }
}
