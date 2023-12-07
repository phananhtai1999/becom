<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserConfigQueryBuilder;
use App\Models\UserConfig;

class UserConfigService extends AbstractService
{
    protected $modelClass = UserConfig::class;

    protected $modelQueryBuilderClass = UserConfigQueryBuilder::class;

    /**
     * @return mixed
     */
    public function myUserConfig()
    {
        return $this->model
            ->where([
                ['user_uuid', auth()->user()],
                ['app_id', auth()->appId()]
            ])
            ->first();
    }
}
