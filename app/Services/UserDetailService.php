<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserDetail;

class UserDetailService extends AbstractService
{
    protected $modelClass = UserDetail::class;

    /**
     * @return mixed
     */
    public function myUserDetail()
    {
        return $this->model
            ->where('user_uuid', auth()->user()->getKey())
            ->first();
    }
}
