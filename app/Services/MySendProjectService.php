<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MySendProjectQueryBuilder;
use App\Models\SendProject;

class MySendProjectService extends AbstractService
{
    protected $modelClass = SendProject::class;

    protected $modelQueryBuilderClass = MySendProjectQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyWebsite($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyWebsite($id)
    {
        $website = $this->showMyWebsite($id);

        return $this->destroy($website->getKey());
    }
}
