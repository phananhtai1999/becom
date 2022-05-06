<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailTemplate;
use App\Models\QueryBuilders\MyMailTemplateQueryBuilder;

class MyMailTemplateService extends AbstractService
{
    protected $modelClass = MailTemplate::class;

    protected $modelQueryBuilderClass = MyMailTemplateQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyMailTemplateByKeyOrAbort($id)
    {
        $mailTemplate = $this->model->select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['mail_templates.uuid', $id]
            ])->first();

        if (!empty($mailTemplate)) {
            return $mailTemplate;
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function deleteMyMailTemplateByKey($id)
    {
        $mailTemplate = $this->model->select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['mail_templates.uuid', $id]
            ])->first();

        if (!empty($mailTemplate)) {
            return $this->destroy($mailTemplate->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }
}
