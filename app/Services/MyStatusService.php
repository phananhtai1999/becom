<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyStatusQueryBuilder;
use App\Models\Status;

class MyStatusService extends AbstractService
{
    protected $modelClass = Status::class;

    protected $modelQueryBuilderClass = MyStatusQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyStatus($id)
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
    public function showMyAndPublicStatus($id)
    {
        return $this->model->where('uuid', $id)->where(function ($query) {
            $query->where('user_uuid', auth()->user()->getkey())
                ->orWhereNull('user_uuid');
        })->firstOrFail();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyStatus($id)
    {
        $status = $this->showMyStatus($id);

        return $this->destroy($status->getKey());
    }
}
