<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MailTemplateQueryBuilder;

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
}
