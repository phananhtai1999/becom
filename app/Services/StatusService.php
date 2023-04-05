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
     * @param $userUuid
     * @return mixed
     */
    public function firstStatusByUserUuid($userUuid)
    {
        return $this->model->select(['uuid', 'name', 'points', 'user_uuid'])->where('user_uuid', $userUuid)->orderBy('points')->first();
    }

    /**
     * @return mixed
     */
    public function firstStatusAdmin()
    {
        return $this->model->select(['uuid', 'name', 'points', 'user_uuid'])->where('user_uuid', null)->orderBy('points')->first();
    }

    /**
     * @return mixed
     */
    public function getAllStatusDefault()
    {
        return $this->model->select(['uuid', 'name', 'points', 'user_uuid'])->where('user_uuid', null)->orderBy('points')->get();
    }

    /**
     * @param $userUuid
     * @return mixed
     */
    public function getAllStatusByUserUuid($userUuid)
    {
        return $this->model->select(['uuid', 'name', 'points', 'user_uuid'])->where('user_uuid', $userUuid)->orderBy('points')->get();
    }

    /**
     * @param $userUuid
     * @return mixed
     */
    public function selectStatusDefault($userUuid)
    {
        return $this->firstStatusByUserUuid($userUuid) ?: $this->firstStatusAdmin();
    }
}
