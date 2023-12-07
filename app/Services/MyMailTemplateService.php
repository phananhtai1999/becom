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
    public function deleteMyMailTemplateByKey($id)
    {
        $mailTemplate = $this->findMyMailTemplateByKeyOrAbort($id);

        return $this->destroy($mailTemplate->getKey());
    }
}
