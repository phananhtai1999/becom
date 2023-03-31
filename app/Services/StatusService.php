<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\StatusQueryBuilder;
use App\Models\Status;

class StatusService extends AbstractService
{
    protected $modelClass = Status::class;

    protected $modelQueryBuilderClass = StatusQueryBuilder::class;

    /**
     * @return mixed
     */
    public function defaultStatus()
    {
        return $this->model->where('user_uuid', null)->orderBy('points', 'ASC')->first();
    }

    /**
     * @return void
     */
    public function getAllStatusDefault()
    {
        return $this->model->select(['uuid', 'name'])->where('user_uuid', null)->get();
    }

    /**
     * @param $userUuid
     * @return void
     */
    public function getAllStatusByUserUuid($userUuid)
    {
        return $this->model->select(['uuid', 'name', 'points', 'user_uuid'])->where('user_uuid', $userUuid)->get();
    }
}
