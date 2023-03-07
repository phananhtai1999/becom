<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyRemindQueryBuilder;
use App\Models\Remind;

class MyRemindService extends AbstractService
{
    protected $modelClass = Remind::class;

    protected $modelQueryBuilderClass = MyRemindQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyRemind($id)
    {
        return  $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyRemind($id)
    {
        $remind = $this->showMyRemind($id);

        return $this->destroy($remind->getKey());
    }
}