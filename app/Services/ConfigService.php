<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Config;
use App\Models\QueryBuilders\ConfigQueryBuilder;

class ConfigService extends AbstractService
{
    protected $modelClass = Config::class;

    protected $modelQueryBuilderClass = ConfigQueryBuilder::class;

    /**
     * @return mixed
     */
    public function loadAllConfig()
    {
        return $this->model->all();
    }

    /**
     * @param $configKey
     * @return mixed
     */
    public function findConfigByKey($configKey)
    {
        return $this->model
            ->where('key', $configKey)
            ->first();
    }
}
