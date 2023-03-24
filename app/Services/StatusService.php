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
     * @return array
     */
    public function firstStatus()
    {
        $silver = $this->model->where([
            ['name->en', Status::STATUS_SILVER],
            ['user_uuid', null]
        ])->first();
        $gold = $this->model->where([
            ['name->en', Status::STATUS_GOLD],
            ['user_uuid', null]
        ])->first();
        $platinum = $this->model->where([
            ['name->en', Status::STATUS_PLATINUM],
            ['user_uuid', null]
        ])->first();
        $diamond = $this->model->where([
            ['name->en', Status::STATUS_DIAMOND],
            ['user_uuid', null]
        ])->first();

        return [
            'silver' => $silver,
            'gold' => $gold,
            'platinum' => $platinum,
            'diamond' => $diamond,
        ];
    }
}
