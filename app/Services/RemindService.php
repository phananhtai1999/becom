<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\RemindQueryBuilder;
use App\Models\Remind;

class RemindService extends AbstractService
{
    protected $modelClass = Remind::class;

    protected $modelQueryBuilderClass = RemindQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function withTrashed($id)
    {
        return $this->model->withTrashed()->where('uuid', $id)->first();
    }
}
