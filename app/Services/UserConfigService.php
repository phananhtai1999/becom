<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserConfig;

class UserConfigService extends AbstractService
{
    protected $modelClass = UserConfig::class;

    /**
     * @return mixed
     */
    public function myUserConfig()
    {
        return $this->model
            ->where('user_uuid', auth()->user()->getKey())
            ->first();
    }
}
