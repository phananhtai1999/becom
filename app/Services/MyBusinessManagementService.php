<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\BusinessManagement;
use App\Models\QueryBuilders\MyBusinessManagementQueryBuilder;

class MyBusinessManagementService extends AbstractService
{
    protected $modelClass = BusinessManagement::class;

    protected $modelQueryBuilderClass = MyBusinessManagementQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyBusinessManagement($id)
    {
        return  $this->findOneWhereOrFail([
            ['owner_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyBusinessManagement($id)
    {
        $website = $this->showMyBusinessManagement($id);

        return $this->destroy($website->getKey());
    }
}
