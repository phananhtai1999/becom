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
     * @param $points
     * @return mixed
     */
    public function firstStatusByPoint($points)
    {
        return $this->model->where([
            ['user_uuid', null],
            ['points', '<=', $points]
        ])->orderBy('points', 'DESC')->first();
    }
}
