<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Position;
use App\Models\QueryBuilders\MyPositionQueryBuilder;

class MyPositionService extends AbstractService
{
    protected $modelClass = Position::class;

    protected $modelQueryBuilderClass = MyPositionQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyPosition($id)
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
    public function deleteMyPosition($id)
    {
        $position = $this->showMyPosition($id);

        return $this->destroy($position->getKey());
    }
}
