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
    public function firstStatus() {
        $silver = $this->model->where('name->en', Status::STATUS_SILVER)->first();
        $gold = $this->model->where('name->en', Status::STATUS_GOLD)->first();
        $platinum = $this->model->where('name->en', Status::STATUS_PLATINUM)->first();
        $diamond = $this->model->where('name->en', Status::STATUS_DIAMOND)->first();

        return [
            'silver' => $silver,
            'gold' => $gold,
            'platinum' => $platinum,
            'diamond' => $diamond,
        ];
    }
}
