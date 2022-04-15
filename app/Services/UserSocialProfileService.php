<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\UserSocialProfile;

class UserSocialProfileService extends AbstractService
{
    protected $modelClass = UserSocialProfile::class;

    /**
     * @param $key
     * @return mixed
     */
    public function findBySocialProfileEmail($key)
    {
        return $this->model->where('social_profile_key', $key)->first();
    }
}
